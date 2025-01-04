<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use App\Http\Requests\Movement\StoreMovementInputRequest;
use App\Http\Requests\Movement\StoreMovementOutputRequest;
use Illuminate\Http\Response;
use App\Models\StoreMaterial;
use App\Models\StoreMovement;
use App\Models\StoreMovementStatus;
use App\Models\StoreMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserStore;

class StoreMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $movements = StoreMovement::with([
            'movementMaterials.material.measurementUnit',
            'status',
            'type',
            'concept',
            'fromStore',
            'toStore',
            'createdBy',
            'updatedBy'
        ])->get()->map(function ($movement) {
            return [
                'id' => $movement->id,
                'created_at' => $movement->created_at,
                'created_by' => $movement->createdBy,
                'updated_at' => $movement->updated_at,
                'updated_by' => $movement->updatedBy,
                'from_store' => $movement->fromStore,
                'to_store' => $movement->toStore,
                'materials' => $movement->movementMaterials->map(function ($movementMaterial) {
                    return [
                        'id' => $movementMaterial->material->id,
                        'name' => $movementMaterial->material->name,
                        'measurement_unit' => $movementMaterial->material->measurementUnit,
                        'quantity' => $movementMaterial->quantity
                    ];
                }),
                'type' => [
                    'id' => $movement->type->id,
                    'name' => $movement->type->name,
                ],
                'status' => [
                    'id' => $movement->status->id,
                    'name' => $movement->status->name,
                ],
                'concept' => [
                    'id' => $movement->concept->id,
                    'name' => $movement->concept->name,
                ],
            ];
        });

        return response($movements, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request): Response
    {
        // checkeo que el usuario actual sea encargado de alguno de los almacenes
        $userId = auth()->id();
        $isFromStoreManager = UserStore::where('user_id', $userId)
            ->where('store_id', $request->from_store_id)
            ->exists();
        
        $isToStoreManager = UserStore::where('user_id', $userId)
            ->where('store_id', $request->to_store_id)
            ->exists();

        if (!$isFromStoreManager && !$isToStoreManager) {
            return response([
                'message' => 'No tienes permisos para crear esta transferencia. Debes ser encargado de al menos uno de los almacenes involucrados.'
            ], 403);
        }

        // checkeo stock de materiales
        foreach ($request->materials as $materialData) {
            $fromStoreMaterial = StoreMaterial::where('store_id', $request->from_store_id)
                ->where('material_id', $materialData['material_id'])
                ->first();

            if (!$fromStoreMaterial || $fromStoreMaterial->quantity < $materialData['quantity']) {
                return response([
                    'message' => 'No hay suficiente stock del material en el almacén de origen',
                    'material_id' => $materialData['material_id'],
                    'available_quantity' => $fromStoreMaterial ? $fromStoreMaterial->quantity : 0
                ], 400);
            }
        }

        // busco type y status por defecto
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
        $transferType = StoreMovementType::where('name', 'Transferencia')->firstOrFail();

        DB::beginTransaction();
        try {
            // creo el movimiento
            $movement = StoreMovement::create([
                'created_by_id' => $userId, 
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'store_movement_type_id' => $transferType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // creo los movementMaterials
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // actualizo stock store origen
                $fromStoreMaterial = StoreMaterial::where('store_id', $request->from_store_id)
                    ->where('material_id', $materialData['material_id'])
                    ->first();

                $fromStoreMaterial->quantity -= $materialData['quantity'];
                $fromStoreMaterial->save();
            }

            DB::commit();
            return response($movement->load('movementMaterials.material'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar el movimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeInput(StoreMovementInputRequest $request): Response
    {
        // busco status y type por defecto
        $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();
        $inputType = StoreMovementType::where('name', 'Ingreso')->firstOrFail();

        DB::beginTransaction();
        try {
            // creo el movimiento
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'store_movement_type_id' => $inputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $acceptedStatus->id
            ]);

            // proceso cada material
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // creo o actualizo el storeMaterial
                $storeMaterial = StoreMaterial::firstOrCreate(
                    [
                        'store_id' => $request->store_id,
                        'material_id' => $materialData['material_id']
                    ],
                    [
                        'quantity' => 0,
                        'minimum_limit' => 0,
                        'critical_limit' => 0
                    ]
                );

                $storeMaterial->quantity += $materialData['quantity'];
                $storeMaterial->save();
            }

            DB::commit();
            return response($movement->load('movementMaterials.material'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar el ingreso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeOutput(StoreMovementOutputRequest $request): Response
    {
        // check stock de materiales
        foreach ($request->materials as $materialData) {
            $storeMaterial = StoreMaterial::where('store_id', $request->store_id)
                ->where('material_id', $materialData['material_id'])
                ->first();

            if (!$storeMaterial || $storeMaterial->quantity < $materialData['quantity']) {
                return response([
                    'message' => 'No hay suficiente stock del material en el almacén',
                    'material_id' => $materialData['material_id'],
                    'available_quantity' => $storeMaterial ? $storeMaterial->quantity : 0
                ], 400);
            }
        }

        // busco status y type por defecto
        $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();
        $outputType = StoreMovementType::where('name', 'Salida')->firstOrFail();

        DB::beginTransaction();
        try {
            // creo el movimiento
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'store_movement_type_id' => $outputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $acceptedStatus->id
            ]);

            // proceso cada material
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // actualizo stock
                $storeMaterial = StoreMaterial::where('store_id', $request->store_id)
                    ->where('material_id', $materialData['material_id'])
                    ->first();

                $storeMaterial->quantity -= $materialData['quantity'];
                $storeMaterial->save();
            }

            DB::commit();
            return response($movement->load('movementMaterials.material'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar la salida',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $movement = StoreMovement::with([
            'movementMaterials.material.measurementUnit',
            'status',
            'type',
            'concept',
            'fromStore',
            'toStore',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        $formatted = [
            'id' => $movement->id,
            'created_at' => $movement->created_at,
            'created_by' => $movement->createdBy,
            'updated_at' => $movement->updated_at,
            'updated_by' => $movement->updatedBy,
            'from_store' => $movement->fromStore,
            'to_store' => $movement->toStore,
            'materials' => $movement->movementMaterials->map(function ($movementMaterial) {
                return [
                    'id' => $movementMaterial->material->id,
                    'name' => $movementMaterial->material->name,
                    'measurement_unit' => $movementMaterial->material->measurementUnit,
                    'quantity' => $movementMaterial->quantity
                ];
            }),
            'type' => [
                'id' => $movement->type->id,
                'name' => $movement->type->name,
            ],
            'status' => [
                'id' => $movement->status->id,
                'name' => $movement->status->name,
            ],
            'concept' => [
                'id' => $movement->concept->id,
                'name' => $movement->concept->name,
            ],
        ];

        return response($formatted, 200);
    }

    /**
     * Accept a pending transfer movement
     */
    public function acceptTransfer(string $id): Response
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            
            // Busco el movimiento y sus materiales
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            // checkeo que sea una transferencia
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Verifico que el usuario actual sea encargado del almacén destino
            $isStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $movement->to_store_id)
                ->exists();

            if (!$isStoreManager) {
                return response([
                    'message' => 'No tienes permisos para aceptar esta transferencia. Solo el encargado del almacén destino puede aceptarla.'
                ], 403);
            }

            // Busco los estados
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();

            // checkeo que esté pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // ciclo cada material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Creo o actualizo el StoreMaterial en store destino
                $toStoreMaterial = StoreMaterial::firstOrCreate(
                    [
                        'store_id' => $movement->to_store_id,
                        'material_id' => $movementMaterial->material_id
                    ],
                    [
                        'quantity' => 0,
                        'minimum_limit' => 0,
                        'critical_limit' => 0
                    ]
                );

                // Sumo la cantidad al store destino
                $toStoreMaterial->quantity += $movementMaterial->quantity;
                $toStoreMaterial->save();
            }

            // Actualizo el estado del movimiento y registro quién lo aceptó
            $movement->store_movement_status_id = $acceptedStatus->id;
            $movement->updated_by_id = $userId;
            $movement->save();

            DB::commit();
            return response($movement->load([
                'movementMaterials.material.measurementUnit',
                'status',
                'type',
                'concept',
                'fromStore',
                'toStore',
                'createdBy',
                'updatedBy'
            ]), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar la aceptación de la transferencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a pending transfer movement
     */
    public function rejectTransfer(string $id): Response
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            
            // Busco el movimiento y sus materiales
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            // checkeo que sea una transferencia
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Verifico que el usuario actual sea encargado del almacén destino
            $isStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $movement->to_store_id)
                ->exists();

            if (!$isStoreManager) {
                return response([
                    'message' => 'No tienes permisos para rechazar esta transferencia. Solo el encargado del almacén destino puede rechazarla.'
                ], 403);
            }

            // Busco los estados
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $rejectedStatus = StoreMovementStatus::where('name', 'Rechazado')->firstOrFail();

            // checkeo que esté pendiente
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            foreach ($movement->movementMaterials as $movementMaterial) {
                // busco el StoreMaterial en el store origen
                $fromStoreMaterial = StoreMaterial::where('store_id', $movement->from_store_id)
                    ->where('material_id', $movementMaterial->material_id)
                    ->firstOrFail();

                // Devuelvo la quantity al store origen
                $fromStoreMaterial->quantity += $movementMaterial->quantity;
                $fromStoreMaterial->save();
            }

            // Actualizo el estado del movimiento y registro quién lo rechazó
            $movement->store_movement_status_id = $rejectedStatus->id;
            $movement->updated_by_id = $userId;
            $movement->save();

            DB::commit();
            return response($movement->load([
                'movementMaterials.material.measurementUnit',
                'status',
                'type',
                'concept',
                'fromStore',
                'toStore',
                'createdBy',
                'updatedBy'
            ]), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar el rechazo de la transferencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a pending transfer movement
     */
    public function cancelTransfer(string $id): Response
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            
            // Busco el movimiento y sus materiales
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            // Verifico que sea una transferencia
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Verifico que el usuario actual sea encargado del store origen o destino
            $isFromStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $movement->from_store_id)
                ->exists();
            $isToStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $movement->to_store_id)
                ->exists();

            if (!$isFromStoreManager && !$isToStoreManager) {
                return response([
                    'message' => 'No tienes permisos para cancelar esta transferencia. Solo el encargado del almacén origen puede cancelarla.'
                ], 403);
            }

            // Busco los estados
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $canceledStatus = StoreMovementStatus::where('name', 'Cancelado')->firstOrFail();

            // Verifico que esté pendiente
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Proceso cada material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Busco el StoreMaterial en el almacén origen
                $fromStoreMaterial = StoreMaterial::where('store_id', $movement->from_store_id)
                    ->where('material_id', $movementMaterial->material_id)
                    ->firstOrFail();

                // Devuelvo la cantidad al almacén origen
                $fromStoreMaterial->quantity += $movementMaterial->quantity;
                $fromStoreMaterial->save();
            }

            // Actualizo el estado del movimiento y registro quién lo canceló
            $movement->store_movement_status_id = $canceledStatus->id;
            $movement->updated_by_id = $userId;
            $movement->save();

            DB::commit();
            return response($movement->load([
                'movementMaterials.material.measurementUnit',
                'status',
                'type',
                'concept',
                'fromStore',
                'toStore',
                'createdBy',
                'updatedBy'
            ]), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar la cancelación de la transferencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexByStore(string $storeId, Request $request): Response
    {
        $query = StoreMovement::with([
            'movementMaterials.material.measurementUnit',
            'status',
            'type',
            'concept',
            'fromStore',
            'toStore',
            'createdBy'
        ])
        ->where(function($query) use ($storeId) {
            $query->where('from_store_id', $storeId)
                  ->orWhere('to_store_id', $storeId);
        });

        // Filter by status if status parameter is present
        if ($request->has('status')) {
            $query->whereHas('status', function($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        $movements = $query->get()
            ->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'created_at' => $movement->created_at,
                    'created_by' => $movement->createdBy,
                    'from_store' => $movement->fromStore,
                    'to_store' => $movement->toStore,
                    'materials' => $movement->movementMaterials->map(function ($movementMaterial) {
                        return [
                            'id' => $movementMaterial->material->id,
                            'name' => $movementMaterial->material->name,
                            'measurement_unit' => $movementMaterial->material->measurementUnit,
                            'quantity' => $movementMaterial->quantity
                        ];
                    }),
                    'type' => [
                        'id' => $movement->type->id,
                        'name' => $movement->type->name,
                    ],
                    'status' => [
                        'id' => $movement->status->id,
                        'name' => $movement->status->name,
                    ],
                    'concept' => [
                        'id' => $movement->concept->id,
                        'name' => $movement->concept->name,
                    ],
                ];
            });

        return response($movements, 200);
    }
}
