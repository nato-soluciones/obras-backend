<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Additional;
use App\Models\Contractor;
use App\Models\CurrentAccountMovementType;
use App\Models\Obra;
use App\Services\AdditionalService;
use App\Services\CurrentAccountService;
use Illuminate\Support\Facades\Log;

class ObraAdditionalController extends Controller
{
    public function index(Request $request, int $obraId): Response
    {
        $additionals = Additional::with(['user' => function ($q) {
            $q->select('id', 'firstname', 'lastname')->withTrashed();
        }])->where('obra_id', $obraId)->get();
        return response($additionals, 200);
    }

    public function store(Request $request, int $obraId): Response
    {

        $obra = Obra::find($obraId);
        if (!$obra) {
            return response(['message' => 'Obra no encontrada'], 404);
        }

        $additionalService = app(AdditionalService::class);
        $additionalData = $request->all();
        $additionalData['obra_id'] = $obraId;
        $additional = $additionalService->createAdditionalWithCategories($additionalData);
        $additionalCost = $additionalService->getAdditionalCostsByProvider($additional);

        // CREAR MOVIMIENTO EN LA CUENTAS CORRIENTES
        $CAService = app(CurrentAccountService::class);

        // Recuperar datos del presupuesto
        $clientId = $obra->budget->client_id;
        $currency = $obra->budget->currency;


        // Arma arrays para crear los movimientos de los proveedores
        $movementType = CurrentAccountMovementType::select('id')
            ->where('entity_type', 'PROVIDER')
            ->where('name', 'Adicionales')
            ->first();

        foreach ($additionalCost as $provider) {
            $CA_Provider = [
                'project_id' => $obra->id,
                'entity_type' => 'PROVIDER',
                'entity_id' => $provider['contractor_id'],
                'currency' => $currency,
            ];
            $CA_movement_provider = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'description' => 'Adicional - Obra ' . $obra->name,
                'amount' => $provider['additional_cost'],
                'reference_entity' => 'adicional',
                'reference_id' => $additional['id'],
                'created_by' => auth()->user()->id
            ];
            $CA_movement_provider = $CAService->CAMovementAdd($CA_Provider, $CA_movement_provider);
        }

        // Arma arrays para crear el movimiento del cliente
        $movementType = CurrentAccountMovementType::select('id')
            ->where('entity_type', 'CLIENT')
            ->where('name', 'Adicionales')
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
            'description' => 'Adicional - Obra ' . $obra->name,
            'amount' => $additional['total'],
            'reference_entity' => 'adicional',
            'reference_id' => $additional['id'],
            'created_by' => auth()->user()->id
        ];

        $CA_movement_client = $CAService->CAMovementAdd($CA_Client, $CA_movement_client);

        return response(['message' => 'Adicional creado correctamente', 'data' => $additional], 201);
    }
    /**
     * Get an additional by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id, int $additionalId): Response
    {
        $additional = Additional::with('categories.activities')->find($additionalId);

        // Carga manualmente el proveedor (contratista) para cada actividad si el campo provider_id no es nulo
        foreach ($additional->categories as $category) {
            foreach ($category->activities as $activity) {
                if ($activity->provider_id !== null) {
                    $contractorBusinessName = Contractor::where('id', $activity->provider_id)->value('business_name');
                    $activity->provider_name = $contractorBusinessName;
                }
            }
        }

        return response($additional, 200);
    }


    /**
     * Edit a additional
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id, int $additionalId): Response
    {
        // Obtiene una instancia del servicio BudgetService
        $additionalService = app(AdditionalService::class);

        // Si la validación pasa, procede con la actualización del presupuesto
        $additional = $additionalService->updateAdditional($additionalId, $request->all());

        return response([
            'message' => 'Additional edited',
            'data' => $additional
        ], 201);
    }

    /**
     * Delete an additional by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $obraId, int $additionalId): Response
    {
        $obra = Obra::find($obraId);
        if (!$obra) {
            return response(['message' => 'Obra no encontrada'], 404);
        }

        $additional = Additional::with('categories.activities')->find($additionalId);

        $additionalService = app(AdditionalService::class);
        $additionalWithCost = $additionalService->getAdditionalCostsByProvider($additional->toArray());
        
        // CREAR MOVIMIENTO EN LA CUENTAS CORRIENTES
        $CAService = app(CurrentAccountService::class);

        // Recuperar datos del presupuesto
        $clientId = $obra->budget->client_id;
        $currency = $obra->budget->currency;


        // Arma arrays para crear los movimientos de los proveedores
        $movementType = CurrentAccountMovementType::select('id')
            ->where('entity_type', 'PROVIDER')
            ->where('name', 'Adicionales - Eliminado')
            ->first();

        foreach ($additionalWithCost as $provider) {
            $CA_Provider = [
                'project_id' => $obra->id,
                'entity_type' => 'PROVIDER',
                'entity_id' => $provider['contractor_id'],
                'currency' => $currency,
            ];
            $CA_movement_provider = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'reference_entity' => 'adicional',
                'reference_id' => $additional['id'],
                'created_by' => auth()->user()->id
            ];
            $CA_movement_provider = $CAService->CAMovementDeleteByReference($CA_Provider, $CA_movement_provider);
        }

        // Arma arrays para crear el movimiento del cliente
        $movementType = CurrentAccountMovementType::select('id')
            ->where('entity_type', 'CLIENT')
            ->where('name', 'Adicionales - Eliminado')
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
            'reference_entity' => 'adicional',
            'reference_id' => $additional['id'],
            'created_by' => auth()->user()->id
        ];

        $CA_movement_client = $CAService->CAMovementDeleteByReference($CA_Client, $CA_movement_client);

        Log::debug($additionalWithCost);
        return response(['message' => 'Additional deleted'], 204);
    }
}
