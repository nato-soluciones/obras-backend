<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Obra;
use App\Models\Outcome;
use App\Models\ObraStage;
use App\Services\AdditionalService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ObraController extends Controller
{
    /**
     * Get all obras
     *
     * @return Response
     */
    public function index(): Response
    {
        $obras = Obra::with(['client' => function($q){
            $q->select('id', 'person_type','firstname', 'lastname', 'business_name', 'deleted_at')->withTrashed();
        }])->get();

        $obras->each(function ($obra) {
            $activeStage = ObraStage::select('id', 'name', 'progress', 'end_date')
                ->where('obra_id', $obra->id)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))
                ->first();

            $obra->active_stage = $activeStage;
        });
        return response($obras, 200);
    }

    /**
     * Create an obra
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $image = $request->file('image');
        $obra = Obra::create($request->all());

        if ($image) {
            $directory = 'public/uploads/obras/' . $obra->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $obra->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $obra->save();

        $budget = $obra->budget;
        $budget->status = 'FINISHED';
        $budget->save();

        return response($obra, 201);
    }

    /**
     * Get an obra by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $obra = Obra::with(['client', 'budget',  'documents', 'additionals' => function ($query) {
            $query->with(['user' => function ($q) {
                $q->withTrashed();
            }]);
        }])->find($id);

        // 'outcomes.contractor',
        // $outcomes = Outcome::where('obra_id', $id)
        //     ->whereNotNull('contractor_id')
        //     ->with('contractor')
        //     ->get();
        // $contractors = $outcomes->pluck('contractor')->unique('id');
        // $obra->contractors = $contractors;

        return response($obra, 200);
    }

    /**
     * Update an obra by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $obra = Obra::find($id);
        $data = $request->except('image');
        $obra->update($data);

        if ($request->hasFile('new_image')) {
            $image = $request->file('new_image');
            $directory = 'public/uploads/obras/' . $obra->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $obra->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
            $obra->save();
        }

        return response($obra, 200);
    }

    /**
     * Delete an obra by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        try {
            $obra = Obra::findOrFail($id);
            $obra->delete();
            return response(['message' => 'Obra eliminada correctamente'], 204);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Obra no encontrada'], 404);
        }
    }

    /**
     * Create a additional for an obra
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function additionals(Request $request, int $id): Response
    {
        $additionalService = app(AdditionalService::class);
        $additionalData = $request->all();
        $additional = $additionalService->createAdditionalWithCategories($additionalData);

        return response(['message' => 'Additional created', 'data' => $additional], 201);
    }

    public function contractors(int $id): Response
    {
        $obra = Obra::findOrFail($id);

        // Recupera los proveedores y el monto presupuestado de cada uno
        $resultBudget = $obra->budget->categories()
            ->join('budgets_categories_activities as bca', 'budgets_categories.id', '=', 'bca.budget_category_id')
            ->join('contractors as c', 'bca.provider_id', '=', 'c.id')
            ->selectRaw('bca.provider_id as contractor_id, c.business_name, c.last_name, c.first_name, c.person_type, ROUND(SUM(bca.unit_cost * bca.quantity), 2) as budgeted_price')
            ->groupBy('bca.provider_id','c.business_name', 'c.last_name', 'c.first_name', 'c.person_type')
            ->get();

        // Recupera los proveedores de adicionales y el monto presupuestado de cada uno
        $resultAdditional = $obra->additionals()
            ->join('additionals_categories as ac', 'ac.additional_id', '=', 'additionals.id')
            ->join('additionals_categories_activities as aca', 'aca.additional_category_id', '=', 'ac.id')
            ->join('contractors as c', 'aca.provider_id', '=', 'c.id')
            ->selectRaw('aca.provider_id as contractor_id, c.business_name, c.last_name, c.first_name, c.person_type, ROUND(SUM(aca.unit_cost * aca.quantity), 2) as budgeted_price')
            ->groupBy('aca.provider_id', 'c.business_name', 'c.last_name', 'c.first_name', 'c.person_type')
            ->get();

        // Recupera los proveedor y el monto pagado de cada uno
        $resultOutcomes = $obra->outcomes()
            ->where('type', 'CONTRACTORS')
            ->selectRaw('outcomes.contractor_id, ROUND(SUM(outcomes.gross_total), 2) as paid_total')
            ->groupBy('outcomes.contractor_id')
            ->get();

        // Calcula el porcentaje de avance de cada proveedor (en base a los proveedores que están en el presupuesto)
        $result = $resultBudget->map(function ($budgetItem) use ($resultOutcomes, $resultAdditional) {
            $outcomeItem = $resultOutcomes->where('contractor_id', $budgetItem->contractor_id)->first();
            $additionalItem = $resultAdditional->where('contractor_id', $budgetItem->contractor_id)->first();
            $paidTotal = $outcomeItem ? $outcomeItem->paid_total : 0;
            $additionalBudgetedPrice = $additionalItem ? $additionalItem->budgeted_price : 0;
            $totalBudgetedPrice = $budgetItem->budgeted_price + $additionalBudgetedPrice;
            $progressPaymentPercentage = $totalBudgetedPrice > 0 ? ($paidTotal / $totalBudgetedPrice) * 100 : 0;

            return [
                'contractor_id' => $budgetItem->contractor_id,
                'business_name' => $budgetItem->business_name,
                'last_name' => $budgetItem->last_name,
                'first_name' => $budgetItem->first_name,
                'person_type' => $budgetItem->person_type,
                'budgeted_price' => $totalBudgetedPrice,
                'paid_total' => $paidTotal,
                'balance' => $totalBudgetedPrice - $paidTotal,
                'progress_payment_percentage' => round($progressPaymentPercentage, 2),
            ];
        })->values();


        // Recupera los proveedores que están en adicionales pero no en presupuesto

        // Obtener todos los contractor_id de $result
        $resultContractorIds = $result->pluck('contractor_id');
        // Filtrar $resultAdditional para obtener los contractor_id que no están en $result
        $missingContractors = $resultAdditional->filter(function ($item) use ($resultContractorIds) {
            return !$resultContractorIds->contains($item->contractor_id);
        });

        // Crear un array con la misma estructura que $result para los contratistas faltantes
        $missingAdditionalsData = $missingContractors->map(function ($additionalItem) use ($resultOutcomes) {
            $outcomeItem = $resultOutcomes->where('contractor_id', $additionalItem->contractor_id)->first();
            $paidTotal = $outcomeItem ? $outcomeItem->paid_total : 0;
            $additionalBudgetedPrice = $additionalItem->budgeted_price;
            $progressPaymentPercentage = $additionalBudgetedPrice > 0 ? ($paidTotal / $additionalBudgetedPrice) * 100 : 0;

            return [
                'contractor_id' => $additionalItem->contractor_id,
                'business_name' => $additionalItem->business_name,
                'last_name'     => $additionalItem->last_name,
                'first_name'    => $additionalItem->first_name,
                'person_type'   => $additionalItem->person_type,
                'budgeted_price' => $additionalBudgetedPrice,
                'paid_total' => $paidTotal,
                'balance' => $additionalBudgetedPrice - $paidTotal,
                'progress_payment_percentage' => round($progressPaymentPercentage, 2),
            ];
        })->values();

        // Combinar $result y $missingAdditionalsData
        $finalResult = $result->merge($missingAdditionalsData);

        return response($finalResult, 200);
    }
}
