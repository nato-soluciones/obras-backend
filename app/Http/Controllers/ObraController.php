<?php

namespace App\Http\Controllers;

use App\Models\Additional;
use App\Models\Budget;
use App\Models\CurrentAccountMovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Obra;
use App\Models\Obra\ObraPlanChargeDetail;
use App\Models\ObraStage;
use App\Services\CurrentAccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObraController extends Controller
{
    public function index()
    {
        $obras = Obra::with(['client' => function ($q) {
            $q->select('id', 'person_type', 'firstname', 'lastname', 'business_name', 'deleted_at')->withTrashed();
        }])->get();

        $obras->each(function ($obra) {
            $activeStage = ObraStage::select('id', 'name', 'progress', 'end_date')
                ->where('obra_id', $obra->id)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))
                ->first();

            $obra->setAttribute('active_stage', $activeStage);
        });
        return response($obras, 200);
    }

    public function getGeneralViewTotals(int $obraId)
    {
        $additionalTotals = Additional::where('obra_id', $obraId)
            ->selectRaw('SUM(total_cost) as total_cost_sum, SUM(total) as total_sum')
            ->first();

        $PCDetailTotal = ObraPlanChargeDetail::whereHas('planCharge', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })->where('type', 'ADJUSTMENT')->sum('total_amount');

        $response = [
            'additional_total_costs' => $additionalTotals->total_cost_sum,
            'additional_totals' => $additionalTotals->total_sum,
            'plan_charge_adjustment_totals' => $PCDetailTotal,
        ];
        return response($response, 200);
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
        $obra = null;

        try {
            // DB::transaction(function () use ($request, $image, $obra) {
            $obraData = $request->all();
            $budget = null;
            if (isset($obraData['budget_id']) && intval($obraData['budget_id']) > 0) {
                $budget = Budget::find($obraData['budget_id']);
                $obraData['currency'] = $budget->currency;
                $obraData['total'] = $budget->total - ($budget->discount_amount ? $budget->discount_amount : 0);
                $obraData['total_cost'] = $budget->total_cost;
                $obraData['client_id'] = $budget->client_id;

                $obraData['covered_area'] = $budget->covered_area ?? null;
                $obraData['semi_covered_area'] = $budget->semi_covered_area ?? null;
            }

            $obra = Obra::create($obraData);

            if ($image) {
                $directory = 'public/uploads/obras/' . $obra->id;
                $imageName = 'image.' . $image->extension();
                $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
                $obra->image = Storage::url($imagePath);

                $absolutePathToDirectory = storage_path('app/' . $directory);
                chmod($absolutePathToDirectory, 0755);

                $obra->save();
            }

            $clientId = $obra->client_id;
            $currency = $obra->currency;
            $totalObra = $obra->total;

            // CREAR MOVIMIENTO EN LA CUENTAS CORRIENTES
            $CAService = app(CurrentAccountService::class);

            // Actualiza presupuesto y prepara las cuentas corrientes de los proveedores
            if ($budget) {
                $budget->status = 'FINISHED';
                $budget->save();

                // Recuperar costos por proveedor del presupuesto
                $resultBudget = $budget->categories()
                    ->join(
                        'budgets_categories_activities as bca',
                        'budgets_categories.id',
                        '=',
                        'bca.budget_category_id'
                    )
                    ->selectRaw('bca.provider_id as contractor_id, ROUND(SUM(bca.unit_cost * bca.quantity), 2) as budgeted_price')
                    ->groupBy('bca.provider_id')
                    ->get();

                // Arma arrays para crear los movimientos de los proveedores
                $movementType = CurrentAccountMovementType::select('id')
                    ->where('entity_type', 'PROVIDER')
                    ->where('name', 'Proyecto')
                    ->first();

                foreach ($resultBudget as $provider) {
                    $CA_Provider = [
                        'project_id' => $obra->id,
                        'entity_type' => 'PROVIDER',
                        'entity_id' => $provider->contractor_id,
                        'currency' => $currency,
                    ];
                    $CA_movement_provider = [
                        'date' => Date('Y-m-d'),
                        'movement_type_id' => $movementType->id,
                        'description' => 'Obra ' . $obra->name,
                        'amount' => $provider->budgeted_price,
                        'reference_entity' => 'obra',
                        'reference_id' => $obra->id,
                        'created_by' => auth()->user()->id
                    ];
                    $CA_movement_provider = $CAService->CAMovementAdd($CA_Provider, $CA_movement_provider);
                }
            }

            // Arma arrays para crear el movimiento del cliente
            $movementType = CurrentAccountMovementType::select('id')
                ->where('entity_type', 'CLIENT')
                ->where('name', 'Proyecto')
                ->first();

            $CA_Client = [
                'project_id' => $obra->id,
                'entity_type' => 'CLIENT',
                'entity_id' => $clientId,
                'currency' => $currency,
            ];
            $CA_movement_client = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'description' => 'Obra ' . $obra->name,
                'amount' => $totalObra,
                'reference_entity' => 'obra',
                'reference_id' => $obra->id,
                'created_by' => auth()->user()->id
            ];

            $CA_movement_client = $CAService->CAMovementAdd($CA_Client, $CA_movement_client);
            // });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['status' => 500, 'message' => 'Error al guardar la obra'], 500);
        }

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
        $obra = Obra::with(['client'])->find($id);

        if ($obra) {
            $obra->has_plan_charge = $obra->planChanges()->exists();
        }
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

    public function contractors(int $id): Response
    {
        $obra = Obra::findOrFail($id);

        // Recupera los proveedores y el monto presupuestado de cada uno
        $resultBudget = collect(); // Inicializas una colección vacía
        if ($obra->budget) {
            $resultBudget = $obra->budget->categories()
                ->join('budgets_categories_activities as bca', 'budgets_categories.id', '=', 'bca.budget_category_id')
                ->join('contractors as c', 'bca.provider_id', '=', 'c.id')
                ->join('contractor_industries as i', 'c.industry', '=', 'i.code')
                ->selectRaw('bca.provider_id as contractor_id, c.business_name, c.last_name, c.first_name, c.person_type, c.type as contractor_type, i.code as industry_code, i.name as industry_name, ROUND(SUM(bca.unit_cost * bca.quantity), 2) as budgeted_price')
                ->groupBy('bca.provider_id', 'c.business_name', 'c.last_name', 'c.first_name', 'c.person_type', 'c.type', 'i.code', 'i.name')
                ->get();
        }

        // Recupera los proveedores de adicionales y el monto presupuestado de cada uno
        $resultAdditional = $obra->additionals()
            ->join('additionals_categories as ac', 'ac.additional_id', '=', 'additionals.id')
            ->join('additionals_categories_activities as aca', 'aca.additional_category_id', '=', 'ac.id')
            ->join('contractors as c', 'aca.provider_id', '=', 'c.id')
            ->join('contractor_industries as i', 'c.industry', '=', 'i.code')
            ->selectRaw('aca.provider_id as contractor_id, c.business_name, c.last_name, c.first_name, c.person_type, c.type as contractor_type, i.code as industry_code, i.name as industry_name, ROUND(SUM(aca.unit_cost * aca.quantity), 2) as budgeted_price')
            ->groupBy('aca.provider_id', 'c.business_name', 'c.last_name', 'c.first_name', 'c.person_type', 'c.type', 'i.code', 'i.name')
            ->get();

        // Recupera los proveedor y el monto pagado de cada uno
        $resultOutcomes = $obra->outcomes()
            ->where('type', 'CONTRACTORS')
            ->selectRaw('outcomes.contractor_id, ROUND(SUM(outcomes.total), 2) as paid_total')
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
                'contractor_type' => $budgetItem->contractor_type,
                'contractor_id' => $budgetItem->contractor_id,
                'business_name' => $budgetItem->business_name,
                'last_name' => $budgetItem->last_name,
                'first_name' => $budgetItem->first_name,
                'person_type' => $budgetItem->person_type,
                'industry_code' => $budgetItem->industry_code,
                'industry_name' => $budgetItem->industry_name,
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
                'contractor_type' => $additionalItem->contractor_type,
                'contractor_id' => $additionalItem->contractor_id,
                'business_name' => $additionalItem->business_name,
                'last_name'     => $additionalItem->last_name,
                'first_name'    => $additionalItem->first_name,
                'person_type'   => $additionalItem->person_type,
                'industry_code' => $additionalItem->industry_code,
                'industry_name' => $additionalItem->industry_name,
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
