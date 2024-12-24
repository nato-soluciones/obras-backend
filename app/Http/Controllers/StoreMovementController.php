<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Store;
use App\Models\Material;
use App\Models\StoreMaterial;
use App\Models\StoreMovement;
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
    public function store(Request $request): Response
    {
        try {
            $fromStore = Store::findOrFail($request->from_store_id);
            $toStore = Store::findOrFail($request->to_store_id);
            $material = Material::findOrFail($request->material_id);

            $fromStoreMaterial = StoreMaterial::where('store_id', $fromStore->id)
                ->where('material_id', $material->id)
                ->first();

            // check si hay suficiente stock
            if (!$fromStoreMaterial || $fromStoreMaterial->quantity < $request->quantity) {
                return response([
                    'message' => 'No hay suficiente stock en el almacén de origen',
                    'available_quantity' => $fromStoreMaterial ? $fromStoreMaterial->quantity : 0
                ], 400);
            }

            DB::beginTransaction();
            try {
                $movement = StoreMovement::create($request->all());
                $fromStoreMaterial->quantity -= $request->quantity;
                $fromStoreMaterial->save();

                $toStoreMaterial = StoreMaterial::firstOrCreate(
                    [
                        'store_id' => $toStore->id,
                        'material_id' => $material->id
                    ],
                    [
                        'quantity' => 0,
                        'minimum_limit' => 0,
                        'critical_limit' => 0
                    ]
                );
                
                $toStoreMaterial->quantity += $request->quantity;
                $toStoreMaterial->save();

                DB::commit();
                return response($movement, 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ModelNotFoundException $e) {
            return response([
                'message' => 'No se encontró alguno de los elementos necesarios para el movimiento'
            ], 404);
        } catch (\Exception $e) {
            return response([
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
