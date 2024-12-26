<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use Illuminate\Http\Response;
use App\Models\StoreMaterial;
use App\Models\StoreMovement;
use App\Models\StoreMovementStatus;
use Illuminate\Support\Facades\DB;

class StoreMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $movements = StoreMovement::select('id', 'from_store_id', 'to_store_id', 'material_id', 'quantity', 'status')->get();

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

        // Buscar el status Pendiente
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->firstOrFail();

        DB::beginTransaction();

        try {
            // Crear el movimiento
            $movement = StoreMovement::create([
                'created_by_id' => $request->created_by_id,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'material_id' => $request->material_id,
                'quantity' => $request->quantity,
                'store_movement_type_id' => $request->store_movement_type_id,
                'store_movement_concept_id' => $request->store_movement_concept_id,
                'store_movement_status_id' => $pendingStatus->id
            ]);

            // actualizar stock en store origen restando quantity
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

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $movement = StoreMovement::select('id', 'from_store_id', 'to_store_id', 'material_id', 'quantity', 'status')->find($id);

        return response($movement, 200);
    }
}
