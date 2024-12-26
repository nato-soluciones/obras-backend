<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use App\Http\Requests\Movement\StoreMovementInputRequest;
use App\Http\Requests\Movement\StoreMovementOutputRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\StoreMaterial;
use App\Models\StoreMovement;
use App\Models\StoreMovementStatus;
use App\Models\StoreMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
            'createdBy'
        ])->get()->map(function ($movement) {
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request): Response
    {
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
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'store_movement_type_id' => $transferType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // creo los movimientos de materiales
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
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
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
                'store_movement_status_id' => $pendingStatus->id
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
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
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
                'store_movement_status_id' => $pendingStatus->id
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
            'createdBy'
        ])->findOrFail($id);

        $formatted = [
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

        return response($formatted, 200);
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
