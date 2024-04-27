<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Obra;
use App\Models\Outcome;
use App\Models\ObraStage;
use App\Services\AdditionalService;
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
        $obras = Obra::with('client')->get();

        $obras->each(function ($obra) {
            $activeStage = ObraStage::select('id', 'name', 'progress', 'end_date')
                ->where('obra_id', $obra->id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
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
        $obra = Obra::with(['client', 'budget', 'incomes', 'outcomes.contractor', 'documents', 'additionals.user'])->find($id);

        $outcomes = Outcome::where('obra_id', $id)
            ->whereNotNull('contractor_id')
            ->with('contractor')
            ->get();
        $contractors = $outcomes->pluck('contractor')->unique('id');
        $obra->contractors = $contractors;

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
        $obra = Obra::find($id);
        $obra->delete();
        return response(['message' => 'Obra deleted'], 204);
    }

    /**
     * Store a document for an obra
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function documents(Request $request, int $id): Response
    {
        $obra = Obra::find($id);
        $name = $request->input('name');
        $category = $request->input('category');
        $document = $request->file('file');

        $directory = 'public/uploads/obras/' . $obra->id;
        $documentName = $document->getClientOriginalName();
        $documentPath = Storage::putFileAs($directory, $document, $documentName, 'public');

        $obra->documents()->create([
            'name' => $name,
            'category' => $category,
            'path' => Storage::url($documentPath),
        ]);

        $absolutePathToDirectory = storage_path('app/' . $directory);
        chmod($absolutePathToDirectory, 0755);

        return response(['message' => 'Document uploaded'], 201);
    }

    /**
     * Delete a document for an obra
     *
     * @param int $id
     * @param int $documentId
     * @return Response
     */
    public function deleteDocument(int $id, int $documentId): Response
    {
        $obra = Obra::find($id);
        $document = $obra->documents()->find($documentId);
        $document->delete();

        $absolutePathToFile = storage_path('app/' . $document->path);
        unlink($absolutePathToFile);

        return response(['message' => 'Document deleted'], 204);
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
        $obra = Obra::find($id);

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
            ->selectRaw('bca.provider_id as contractor_id, c.business_name, ROUND(SUM(bca.unit_cost * bca.quantity), 2) as budgeted_price')
            ->groupBy('bca.provider_id', 'c.business_name')
            ->get();

        // Recupera los proveedores de adicionales y el monto presupuestado de cada uno
        $resultAdditional = $obra->additionals()
            ->join('additionals_categories as ac', 'ac.additional_id', '=', 'additionals.id')
            ->join('additionals_categories_activities as aca', 'aca.additional_category_id', '=', 'ac.id')
            ->join('contractors as c', 'aca.provider_id', '=', 'c.id')
            ->selectRaw('aca.provider_id as contractor_id, c.business_name, ROUND(SUM(aca.unit_cost * aca.quantity), 2) as budgeted_price')
            ->groupBy('aca.provider_id', 'c.business_name')
            ->get();

        // Recupera los proveedor y el monto pagado de cada uno
        $resultOutcomes = $obra->outcomes()
            ->where('type', 'CONTRACTORS')
            ->selectRaw('outcomes.contractor_id, ROUND(SUM(outcomes.gross_total), 2) as paid_total')
            ->groupBy('outcomes.contractor_id')
            ->get();

        // Calcula el porcentaje de avance de cada proveedor (en base a los proveedores que estan en el presupuesto)
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
                'budgeted_price' => $totalBudgetedPrice,
                'paid_total' => $paidTotal,
                'balance' => $totalBudgetedPrice - $paidTotal,
                'progress_payment_percentage' => round($progressPaymentPercentage, 2),
            ];
        })->values();


        // Recupera los proveedores que estan en adicionales pero no en presupuesto

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
