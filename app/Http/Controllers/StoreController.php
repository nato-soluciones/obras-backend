<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Models\Store;
use App\Models\StoreMovement;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\UserStore;
use App\Enums\MaterialLimitStatus;
use App\Http\Services\AppSettingService;
use App\Models\StoreMaterial;

class StoreController extends Controller
{
    
    private AppSettingService $appSettingService;
    private float $almostPercentage; 
    
    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
        $settings = $this->appSettingService->getSettingsByModule('STOCK');
        $this->almostPercentage = $settings['LIMIT_PROXIMITY_PERCENTAGE'] ?? 0.10;
    }


    /**
     * Display a listing of the resource.
     */
    public function indexWithMaterials(): Response
    {
        $stores = Store::with(['materialsStore.material', 'userStores.user'])->get();

        $formatted = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'description' => $store->description,
                'manager' => $store->userStores->first()?->user,
                'materials' => $store->materialsStore->map(function ($materialStore) {
                    $limitStatus = $this->calculateLimitStatus($materialStore);
                    
                    return [
                        'material_store_id' => $materialStore->id,
                        'material_id' => $materialStore->material_id,
                        'name' => $materialStore->material->name,
                        'description' => $materialStore->material->description,
                        'quantity' => $materialStore->quantity,
                        'minimum_limit' => $materialStore->minimum_limit,
                        'critical_limit' => $materialStore->critical_limit,
                        'limit_status' => $limitStatus->value
                    ];
                })
            ];
        });

        return response($formatted, 200);
    }

    private function calculateLimitStatus(StoreMaterial $materialStore): MaterialLimitStatus
    {
        $quantity = $materialStore->quantity;
        $criticalLimit = $materialStore->critical_limit;
        $minimumLimit = $materialStore->minimum_limit;
        
        // calculating ranges for almost_critical and almost_minimum
        $criticalRange = ($minimumLimit - $criticalLimit) * $this->almostPercentage;
        $minimumRange = $minimumLimit * $this->almostPercentage;
        
        if ($quantity <= $criticalLimit) {
            return MaterialLimitStatus::CRITICAL;
        }
        
        if ($quantity <= $criticalLimit + $criticalRange) {
            return MaterialLimitStatus::ALMOST_CRITICAL;
        }
        
        if ($quantity <= $minimumLimit) {
            return MaterialLimitStatus::MINIMUM;
        }
        
        if ($quantity <= $minimumLimit + $minimumRange) {
            return MaterialLimitStatus::ALMOST_MINIMUM;
        }
        
        return MaterialLimitStatus::NORMAL;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStoreRequest $request): Response
    {
        DB::beginTransaction();
        try {
            $store = Store::create($request->only(['name', 'address', 'description']));
            
            // Create the manager relationship
            UserStore::create([
                'user_id' => $request->manager_id,
                'store_id' => $store->id
            ]);

            DB::commit();
            return response($store->load('userStores.user'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al crear el almacén',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $store = Store::with(['materialsStore.material', 'userStores.user'])->find($id);

        if (!$store) {
            return response([
                'error' => 'Almacén no encontrado'
            ], 404);
        }

        $formatted = [
            'id' => $store->id,
            'name' => $store->name,
            'address' => $store->address,
            'description' => $store->description,
            'manager' => $store->userStores->first()?->user,
            'materials' => $store->materialsStore->map(function ($materialStore) {
                return [
                    'material_id' => $materialStore->material_id,
                    'material_name' => $materialStore->material->name,
                    'description' => $materialStore->material->description,
                    'quantity' => $materialStore->quantity,
                    'minimum_limit' => $materialStore->minimum_limit,
                    'critical_limit' => $materialStore->critical_limit
                ];
            }),
        ];

        return response($formatted, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, string $id): Response
    {
        DB::beginTransaction();
        try {
            $store = Store::findOrFail($id);
            $store->update($request->only(['name', 'address', 'description']));

            // Update manager if provided
            if ($request->has('manager_id')) {
                // Remove existing manager relationships
                UserStore::where('store_id', $store->id)->delete();
                
                // Create new manager relationship
                UserStore::create([
                    'user_id' => $request->manager_id,
                    'store_id' => $store->id
                ]);
            }

            DB::commit();
            return response($store->load('userStores.user'), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response(['error' => 'Almacén no encontrado'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al actualizar el almacén',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();
            return response(['message' => 'Almacén eliminado correctamente'], 204); // este mensaje no se muestra porque 204 es sin content. Está asi en los demas
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Almacén no encontrado'], 404);
        }
    }

    /**
     * Display a listing of stores with their last movement date.
     */
    public function index(): Response
    {
        $stores = Store::with('userStores.user')
            ->select('id', 'name', 'address', 'description')
            ->get()
            ->map(function ($store) {
                // Buscar el último movimiento para este store
                $lastMovement = StoreMovement::where(function($query) use ($store) {
                    $query->where('from_store_id', $store->id)
                          ->orWhere('to_store_id', $store->id);
                })
                ->latest('created_at')
                ->first();

                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'address' => $store->address,
                    'description' => $store->description,
                    'manager' => $store->userStores->first()?->user,
                    'lastMovement' => $lastMovement ? $lastMovement->created_at->format('d-m-Y') : null
                ];
            });

        return response($stores, 200);
    }
}
