<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrentAccount\CreateMovementRequest;
use App\Models\CurrentAccountMovement;
use App\Services\CurrentAccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class CurrentAccountMovementController extends Controller
{
    /////////////////////
    //? Clients Methods
    /////////////////////
    public function indexClients(Request $request, int $clientId, int $projectId, string $currency)
    {
        $entityType = 'CLIENT';
        // $page = $request->input('page', 1);

        $CAService = app(CurrentAccountService::class);
        $respService = $CAService->CAMovementList($entityType, $clientId, $projectId, $currency);
        return response($respService, 200);
    }

    public function storeClient(CreateMovementRequest $request, int $clientId, int $projectId, string $currency)
    {
        $CAData = ['entity_type' => 'CLIENT', 'entity_id' => $clientId, 'project_id' => $projectId, 'currency' => $currency];

        $request->merge(
            [
                'date' => Date('Y-m-d'),
                'reference_entity' => 'api-manual',
                'created_by' => auth()->user()->id
            ]
        );

        // Obtiene una instancia del servicio BudgetService
        $CAService = app(CurrentAccountService::class);
        $respService = $CAService->CAMovementAdd($CAData, $request->all());

        return response($respService, $respService['status']);
    }


    public function updateClient(CreateMovementRequest $request, int $clientId, int $projectId, string $currency, string $movementId)
    {
        $data = $request->only('description', 'observation');
        try {
            $caMovement = CurrentAccountMovement::findOrFail($movementId);
            $caMovement->update($data);
            return response($caMovement, 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Movimiento no encontrado'], 404);
        }
    }
    
    /////////////////////
    //? Providers Methods
    /////////////////////
    public function indexProviders(int $providerId, int $projectId, string $currency)
    {
        $entityType = 'PROVIDER';

        $CAService = app(CurrentAccountService::class);
        $respService = $CAService->CAMovementList($entityType, $providerId, $projectId, $currency);
        return response($respService, 200);
    }

    public function storeProvider(CreateMovementRequest $request, int $providerId, int $projectId, string $currency)
    {
        $CAData = ['entity_type' => 'PROVIDER', 'entity_id' => $providerId, 'project_id' => $projectId, 'currency' => $currency];

        $request->merge(
            [
                'date' => Date('Y-m-d'),
                'reference_entity' => 'api-manual',
                'created_by' => auth()->user()->id
            ]
        );

        // Obtiene una instancia del servicio BudgetService
        $CAService = app(CurrentAccountService::class);
        $respService = $CAService->CAMovementAdd($CAData, $request->all());

        return response($respService, $respService['status']);
    }

    public function updateProvider(CreateMovementRequest $request, int $providerId, int $projectId, string $currency, string $movementId)
    {
        $data = $request->only('description', 'observation');
        try {
            $caMovement = CurrentAccountMovement::findOrFail($movementId);
            $caMovement->update($data);
            return response($caMovement, 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Movimiento no encontrado'], 404);
        }
    }
}
