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

class StoreMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $movements = StoreMovement::with([
            'material.measurementUnit',
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
                'material' => $movement->material,
                'quantity' => $movement->quantity,
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
        // Verificar stock del store de origen
        $fromStoreMaterial = StoreMaterial::where('store_id', $request->from_store_id)
            ->where('material_id', $request->material_id)
            ->first();

        if (!$fromStoreMaterial || $fromStoreMaterial->quantity < $request->quantity) {
            return response()->json([
                'message' => 'No hay suficiente stock en el almacén de origen',
                'available_quantity' => $fromStoreMaterial ? $fromStoreMaterial->quantity : 0
            ], 400);
        }

        // Buscar el status Pendiente y tipo Transferencia
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
        $transferType = StoreMovementType::where('name', 'Transferencia')->firstOrFail();

        DB::beginTransaction();
        try {
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'material_id' => $request->material_id,
                'quantity' => $request->quantity,
                'store_movement_type_id' => $transferType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            $fromStoreMaterial->quantity -= $request->quantity;
            $fromStoreMaterial->save();

            DB::commit();
            return response($movement, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar el movimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeInput(StoreMovementInputRequest $request): Response
    {
        // Buscar el status Pendiente y tipo Ingreso
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
        $inputType = StoreMovementType::where('name', 'Ingreso')->firstOrFail();

        DB::beginTransaction();
        try {
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'material_id' => $request->material_id,
                'quantity' => $request->quantity,
                'store_movement_type_id' => $inputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // Crear o actualizar el StoreMaterial
            $storeMaterial = StoreMaterial::firstOrCreate(
                [
                    'store_id' => $request->store_id,
                    'material_id' => $request->material_id
                ],
                [
                    'quantity' => 0,
                    'minimum_limit' => 0,
                    'critical_limit' => 0
                ]
            );

            $storeMaterial->quantity += $request->quantity;
            $storeMaterial->save();

            DB::commit();
            return response($movement, 201);

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
        // Verificar stock del store
        $storeMaterial = StoreMaterial::where('store_id', $request->store_id)
            ->where('material_id', $request->material_id)
            ->first();

        if (!$storeMaterial || $storeMaterial->quantity < $request->quantity) {
            return response([
                'message' => 'No hay suficiente stock en el almacén',
                'available_quantity' => $storeMaterial ? $storeMaterial->quantity : 0
            ], 400);
        }

        // Buscar el status Pendiente y tipo Salida
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();
        $outputType = StoreMovementType::where('name', 'Salida')->firstOrFail();

        DB::beginTransaction();
        try {
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->store_id,
                'to_store_id' => $request->store_id,
                'material_id' => $request->material_id,
                'quantity' => $request->quantity,
                'store_movement_type_id' => $outputType->id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            $storeMaterial->quantity -= $request->quantity;
            $storeMaterial->save();

            DB::commit();
            return response($movement, 201);

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
            'material.measurementUnit',
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
            'material' => $movement->material,
            'quantity' => $movement->quantity,
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
}
