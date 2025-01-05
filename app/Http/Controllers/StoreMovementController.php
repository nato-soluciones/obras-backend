<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use App\Http\Requests\Movement\StoreMovementInputRequest;
use App\Http\Requests\Movement\StoreMovementOutputRequest;
use App\Http\Services\AppSettingService;
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
    private AppSettingService $appSettingService;
    private bool $isCriticalLimitBlock;

    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
        $settings = $this->appSettingService->getSettingsByModule('STOCK');
        $this->isCriticalLimitBlock = $settings['CRITICAL_LIMIT_BLOCK'] ?? false;
    }

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
        // User permissions check
        $userId = auth()->id();
        $isFromStoreManager = UserStore::where('user_id', $userId)
            ->where('store_id', $request->from_store_id)
            ->exists();
        
        $isToStoreManager = UserStore::where('user_id', $userId)
            ->where('store_id', $request->to_store_id)
            ->exists();

        if (!$isFromStoreManager && !$isToStoreManager) {
            return response([
                'success' => false,
                'message' => 'No tienes permisos para crear esta transferencia. Debes ser encargado de al menos uno de los almacenes involucrados.',
                'limits' => [],
                'data' => null
            ], 403);
        }

        // Arrays to track limit violations
        $exceededLimits = [];
        $criticalLimitsExceeded = [];

        // Check stock and limits
        foreach ($request->materials as $materialData) {
            $fromStoreMaterial = StoreMaterial::where('store_id', $request->from_store_id)
                ->where('material_id', $materialData['material_id'])
                ->first();

            if (!$fromStoreMaterial || $fromStoreMaterial->quantity < $materialData['quantity']) {
                return response([
                    'success' => false,
                    'message' => 'No hay suficiente stock del material en el almacén de origen',
                    'limits' => [],
                    'data' => [
                        'material_id' => $materialData['material_id'],
                        'available_quantity' => $fromStoreMaterial ? $fromStoreMaterial->quantity : 0
                    ]
                ], 400);
            }

            // Calculate remaining stock after movement
            $resultingStock = $fromStoreMaterial->quantity - $materialData['quantity'];

            // Check if hitting any limits
            if ($resultingStock < $fromStoreMaterial->critical_limit) {
                if ($this->isCriticalLimitBlock) {
                    $criticalLimitsExceeded[] = [
                        'materialId' => $materialData['material_id'],
                        'limitType' => 'critical',
                        // 'materialName' => $fromStoreMaterial->material->name,
                        // 'currentStock' => $fromStoreMaterial->quantity,
                        // 'resultingStock' => $resultingStock,
                        // 'criticalLimit' => $fromStoreMaterial->critical_limit
                    ];
                } else {
                    $exceededLimits[] = [
                        'materialId' => $materialData['material_id'],
                        'limitType' => 'critical'
                    ];
                }
            } elseif ($resultingStock < $fromStoreMaterial->minimum_limit) {
                $exceededLimits[] = [
                    'materialId' => $materialData['material_id'],
                    'limitType' => 'minimum'
                ];
            }
        }

        // if critical limits are hit and blocking is on, return error
        if ($this->isCriticalLimitBlock && !empty($criticalLimitsExceeded)) {
            return response([
                'success' => false,
                'message' => 'No se puede realizar la transferencia. Los siguientes materiales caerían por debajo del límite crítico:',
                'limits' => $criticalLimitsExceeded,
                'data' => null
            ], 400);
        }

        // create the movement
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
        $transferType = StoreMovementType::where('name', 'Transferencia')->firstOrFail();

        DB::beginTransaction();
        try {
            $movement = StoreMovement::create([
                'created_by_id' => $userId,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'store_movement_type_id' => $transferType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // create movement materials and update stock
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // Update source store stock
                $fromStoreMaterial = StoreMaterial::where('store_id', $request->from_store_id)
                    ->where('material_id', $materialData['material_id'])
                    ->first();

                $fromStoreMaterial->quantity -= $materialData['quantity'];
                $fromStoreMaterial->save();
            }

            DB::commit();

            $message = 'Transferencia creada exitosamente';
            if (!empty($exceededLimits)) {
                $message .= '. Algunos materiales quedarán por debajo de sus límites establecidos';
            }

            return response([
                'success' => true,
                'message' => $message,
                'limits' => $exceededLimits,
                'data' => $movement->load('movementMaterials.material')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'success' => false,
                'message' => 'Error al procesar el movimiento: ' . $e->getMessage(),
                'limits' => [],
                'data' => null
            ], 500);
        }
    }

    public function storeInput(StoreMovementInputRequest $request): Response
    {
        // get default status and type
        $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();
        $inputType = StoreMovementType::where('name', 'Ingreso')->firstOrFail();

        DB::beginTransaction();
        try {
            // create movement
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'store_movement_type_id' => $inputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $acceptedStatus->id
            ]);

            // process each material
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // create or update storeMaterial
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
        // Check stock 
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

        // Get default status and type
        $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();
        $outputType = StoreMovementType::where('name', 'Salida')->firstOrFail();

        DB::beginTransaction();
        try {
            // create movement
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'store_movement_type_id' => $outputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $acceptedStatus->id
            ]);

            // Process each material
            foreach ($request->materials as $materialData) {
                $movement->movementMaterials()->create([
                    'material_id' => $materialData['material_id'],
                    'quantity' => $materialData['quantity']
                ]);

                // update stock
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

            // validate for a transfer type
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Check if user is manager of destination store
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

            //check it is pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Find the material in destination store
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

            // update movement status and track who accepted it
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

            // validate transfer type
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Check if user manages the destination store
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

            // Make sure it's still pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Find the material in source store
                $fromStoreMaterial = StoreMaterial::where('store_id', $movement->from_store_id)
                    ->where('material_id', $movementMaterial->material_id)
                    ->firstOrFail();

                // Return quantity to source store
                $fromStoreMaterial->quantity += $movementMaterial->quantity;
                $fromStoreMaterial->save();
            }

            // Update movement status and track who rejected it
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

            // Make sure its a transfer
            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Check if user manages the source store or destination
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

            // Make sure it's still pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Find the material in source store
                $fromStoreMaterial = StoreMaterial::where('store_id', $movement->from_store_id)
                    ->where('material_id', $movementMaterial->material_id)
                    ->firstOrFail();

                // Return quantity to source store
                $fromStoreMaterial->quantity += $movementMaterial->quantity;
                $fromStoreMaterial->save();
            }

            // Update movement status and track who canceled it
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
