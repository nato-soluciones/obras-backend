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
use App\Models\Store;

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
        // Check if stores exist
        $fromStore = Store::find($request->from_store_id);
        $toStore = Store::find($request->to_store_id);

        if (!$fromStore || !$toStore) {
            return response([
                'success' => false,
                'message' => !$fromStore ? 'El almacén de origen no existe' : 'El almacén de destino no existe',
                'limits' => [],
                'data' => null
            ], 404);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response([
                'success' => false,
                'message' => 'Usuario no autenticado.',
                'limits' => [],
                'data' => null
            ], 401);
        }

        $userId = auth()->id();
        $user = auth()->user();

        // Skip manager validation for SUPERADMIN
        if (!$user->hasRole('SUPERADMIN')) {
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
            // Create movement
            $movement = StoreMovement::create([
                'created_by_id' => $userId,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'store_movement_type_id' => $transferType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // Create movement materials
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
        // Check if store exists
        $store = Store::find($request->store_id);
        if (!$store) {
            return response([
                'message' => 'El almacén especificado no existe',
            ], 404);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response([
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $userId = auth()->id();
        $user = auth()->user();

        // Skip manager validation for SUPERADMIN
        if (!$user->hasRole('SUPERADMIN')) {
            $isStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $request->store_id)
                ->exists();

            if (!$isStoreManager) {
                return response([
                    'message' => 'No tienes permisos para crear este ingreso. Debes ser encargado del almacén.'
                ], 403);
            }
        }

        // Get default status and type
        $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();
        $inputType = StoreMovementType::where('name', 'Ingreso')->firstOrFail();

        DB::beginTransaction();
        try {
            // create movement
            $movement = StoreMovement::create([
                'created_by_id' => $userId,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'store_movement_type_id' => $inputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $acceptedStatus->id
            ]);

            // process each material
            // Process each material
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
        // Check if store exists
        $store = Store::find($request->store_id);
        if (!$store) {
            return response([
                'message' => 'El almacén especificado no existe',
            ], 404);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response([
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $userId = auth()->id();
        $user = auth()->user();

        // Skip manager validation for SUPERADMIN
        if (!$user->hasRole('SUPERADMIN')) {
            $isStoreManager = UserStore::where('user_id', $userId)
                ->where('store_id', $request->store_id)
                ->exists();

            if (!$isStoreManager) {
                return response([
                    'message' => 'No tienes permisos para crear esta salida. Debes ser encargado del almacén.'
                ], 403);
            }
        }

        // Check materials stock
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
                'created_by_id' => $userId,
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
        ])->find($id);

        if (!$movement) {
            return response([
                'error' => 'Movimiento no encontrado'
            ], 404);
        }

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
            $user = auth()->user();
            
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Skip manager validation for SUPERADMIN
            if (!$user->hasRole('SUPERADMIN')) {
            
                $isToStoreManager = UserStore::where('user_id', $userId)
                    ->where('store_id', $movement->to_store_id)
                    ->exists();

                if (!$isToStoreManager) {
                    return response([
                        'message' => 'No tienes permisos para aceptar esta transferencia. Solo el encargado del almacén destino puede aceptarla.'
                    ], 403);
                }
            }

            // Get statuses
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();

            // Check if pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Create or update destination store material
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

                // Add quantity to destination store
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
            $user = auth()->user();
            
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Skip manager validation for SUPERADMIN
            if (!$user->hasRole('SUPERADMIN')) {
            
                $isToStoreManager = UserStore::where('user_id', $userId)
                    ->where('store_id', $movement->to_store_id)
                    ->exists();

                if (!$isToStoreManager) {
                    return response([
                        'message' => 'No tienes permisos para rechazar esta transferencia. Solo el encargado del almacén destino puede rechazarla.'
                    ], 403);
                }
            }

            // Get statuses
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $rejectedStatus = StoreMovementStatus::where('name', 'Rechazado')->firstOrFail();

            // Check if pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Find source store material
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
            $user = auth()->user();
            
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Skip manager validation for SUPERADMIN
            if (!$user->hasRole('SUPERADMIN')) {
                $isFromStoreManager = UserStore::where('user_id', $userId)
                    ->where('store_id', $movement->from_store_id)
                    ->exists();
            
                if (!$isFromStoreManager) {
                    return response([
                        'message' => 'No tienes permisos para cancelar esta transferencia. Solo el encargado del almacén origen puede cancelarla.'
                    ], 403);
                }
            }

            // Get statuses
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $canceledStatus = StoreMovementStatus::where('name', 'Cancelado')->firstOrFail();

            // Check if pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // Process each material
            foreach ($movement->movementMaterials as $movementMaterial) {
                // Find source store material
                $fromStoreMaterial = StoreMaterial::where('store_id', $movement->from_store_id)
                    ->where('material_id', $movementMaterial->material_id)
                    ->firstOrFail();

                // Return quantity to source store
                $fromStoreMaterial->quantity += $movementMaterial->quantity;
                $fromStoreMaterial->save();
            }

            // Update movement status and who canceled it
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

    public function indexByMaterialStore(string $storeMaterialId, Request $request): Response
    {
        $storeMaterial = StoreMaterial::find($storeMaterialId);

        if (!$storeMaterial) {
            return response([
                'message' => 'El material especificado no existe en el almacén',
            ], 404);
        }

        $movements = StoreMovement::with([
            'movementMaterials.material.measurementUnit',
            'status',
            'type',
            'concept',
            'fromStore',
            'toStore',
            'createdBy'
        ])
        ->whereHas('movementMaterials', function($query) use ($storeMaterial) {
            $query->where('material_id', $storeMaterial->material_id);
        })
        ->where(function($query) use ($storeMaterial) {
            $query->where('from_store_id', $storeMaterial->store_id)
                  ->orWhere('to_store_id', $storeMaterial->store_id);
        })
        ->get()
        ->map(function ($movement) use ($storeMaterial) {
            $materialData = $movement->movementMaterials->firstWhere('material_id', $storeMaterial->material_id);
            return [
            'id' => $movement->id,
            'created_at' => $movement->created_at,
            'created_by' => $movement->createdBy,        
            'measurement_unit' => $materialData->material->measurementUnit,
            'quantity' => $materialData->quantity,
            'type' => [
                'id' => $movement->type->id,
                'name' => $movement->type->name,
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
     * Reject a transfer with quantity adjustments
     */
    public function rejectTransferWithAdjustment(Request $request, string $id): Response
    {
        $request->validate([
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.received_quantity' => 'required|numeric|min:0',
            'store_movement_concept_id' => 'required|exists:store_movement_concepts,id'
        ]);

        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $user = auth()->user();
            
            $movement = StoreMovement::with(['movementMaterials.material', 'type'])
                ->findOrFail($id);

            if ($movement->type->name !== 'Transferencia') {
                return response([
                    'message' => 'El movimiento no es una transferencia'
                ], 400);
            }

            // Skip manager validation for SUPERADMIN
            if (!$user->hasRole('SUPERADMIN')) {
                $isToStoreManager = UserStore::where('user_id', $userId)
                    ->where('store_id', $movement->to_store_id)
                    ->exists();

                if (!$isToStoreManager) {
                    return response([
                        'message' => 'No tienes permisos para rechazar esta transferencia. Solo el encargado del almacén destino puede rechazarla.'
                    ], 403);
                }
            }

            // Get statuses
            $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
            $rejectedStatus = StoreMovementStatus::where('name', 'Rechazado')->firstOrFail();
            $acceptedStatus = StoreMovementStatus::where('name', 'Aprobado')->firstOrFail();

            // Check if pending
            if ($movement->store_movement_status_id !== $pendingStatus->id) {
                return response([
                    'message' => 'La transferencia no está en estado pendiente'
                ], 400);
            }

            // First accept the transfer to add original quantities
            foreach ($movement->movementMaterials as $movementMaterial) {
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

                $toStoreMaterial->quantity += $movementMaterial->quantity;
                $toStoreMaterial->save();
            }

            // Then create adjustment movements for differences
            $inputType = StoreMovementType::where('name', 'Ingreso')->firstOrFail();
            $outputType = StoreMovementType::where('name', 'Salida')->firstOrFail();

            $adjustmentInputs = [];
            $adjustmentOutputs = [];

            foreach ($request->materials as $materialData) {
                $originalMaterial = $movement->movementMaterials
                    ->where('material_id', $materialData['material_id'])
                    ->first();

                if (!$originalMaterial) {
                    throw new \Exception("Material ID {$materialData['material_id']} no estaba en la transferencia original");
                }

                $difference = $materialData['received_quantity'] - $originalMaterial->quantity;

                if ($difference < 0) {
                    // Create output for missing quantity
                    $adjustmentOutputs[] = [
                        'material_id' => $materialData['material_id'],
                        'quantity' => abs($difference)
                    ];
                } elseif ($difference > 0) {
                    // Create input for extra quantity
                    $adjustmentInputs[] = [
                        'material_id' => $materialData['material_id'],
                        'quantity' => $difference
                    ];
                }
            }

            // Create output movement if needed
            if (!empty($adjustmentOutputs)) {
                $outputMovement = StoreMovement::create([
                    'created_by_id' => $userId,
                    'from_store_id' => $movement->to_store_id,
                    'to_store_id' => $movement->to_store_id,
                    'store_movement_type_id' => $outputType->id,
                    'store_movement_concept_id' => $request->store_movement_concept_id,
                    'store_movement_status_id' => $acceptedStatus->id
                ]);

                foreach ($adjustmentOutputs as $output) {
                    $outputMovement->movementMaterials()->create($output);
                    
                    $storeMaterial = StoreMaterial::where('store_id', $movement->to_store_id)
                        ->where('material_id', $output['material_id'])
                        ->first();
                    
                    $storeMaterial->quantity -= $output['quantity'];
                    $storeMaterial->save();
                }
            }

            // Create input movement if needed
            if (!empty($adjustmentInputs)) {
                $inputMovement = StoreMovement::create([
                    'created_by_id' => $userId,
                    'from_store_id' => $movement->to_store_id,
                    'to_store_id' => $movement->to_store_id,
                    'store_movement_type_id' => $inputType->id,
                    'store_movement_concept_id' => $request->store_movement_concept_id,
                    'store_movement_status_id' => $acceptedStatus->id
                ]);

                foreach ($adjustmentInputs as $input) {
                    $inputMovement->movementMaterials()->create($input);
                    
                    $storeMaterial = StoreMaterial::where('store_id', $movement->to_store_id)
                        ->where('material_id', $input['material_id'])
                        ->first();
                    
                    $storeMaterial->quantity += $input['quantity'];
                    $storeMaterial->save();
                }
            }

            // Update original movement status
            $movement->store_movement_status_id = $rejectedStatus->id;
            $movement->updated_by_id = $userId;
            $movement->save();

            DB::commit();
            
            $response = [
                'message' => 'Transferencia rechazada con ajustes',
                'original_movement' => $movement->load([
                    'movementMaterials.material.measurementUnit',
                    'status',
                    'type',
                    'concept',
                    'fromStore',
                    'toStore',
                    'createdBy',
                    'updatedBy'
                ])
            ];

            if (!empty($adjustmentOutputs)) {
                $response['output_movement'] = $outputMovement->load('movementMaterials.material');
            }
            if (!empty($adjustmentInputs)) {
                $response['input_movement'] = $inputMovement->load('movementMaterials.material');
            }

            return response($response, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Error al procesar el rechazo con ajustes de la transferencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
