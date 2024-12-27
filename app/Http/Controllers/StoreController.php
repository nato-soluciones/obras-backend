<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Models\Store;
use App\Models\StoreMovement;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class StoreController extends Controller
{

    
    /**
     * Display a listing of the resource.
     */
    public function indexWithMaterials(): Response
    {
        $stores = Store::with(['materialsStore.material'])->get();

        $formatted = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'materials' => $store->materialsStore->map(function ($materialStore) {
                    return [
                        'material_id' => $materialStore->material_id,
                        'name' => $materialStore->material->name,
                        'description' => $materialStore->material->description,
                        'quantity' => $materialStore->quantity,
                        'minimum_limit' => $materialStore->minimum_limit,
                        'critical_limit' => $materialStore->critical_limit
                    ];
                })
            ];
        });

        return response($formatted, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStoreRequest $request): Response
    {
        $store = Store::create($request->validated());
        return response($store, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $store = Store::with(['materialsStore.material'])->find($id);

    if (!$store) {
        return response()->json(['error' => 'Almacén no encontrado'], 404);
    }

    $formatted = [
        'id' => $store->id,
        'name' => $store->name,
        'address' => $store->address,
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
    public function update(UpdateStoreRequest $request, string $id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->update($request->all());
            return response($store, 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Almacén no encontrado'], 404);
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
        $stores = Store::select('id', 'name', 'address')
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
                    'lastMovement' => $lastMovement ? $lastMovement->created_at->format('d-m-Y') : null
                ];
            });

        return response($stores, 200);
    }
}
