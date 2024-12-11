<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\CreateStoreRequest;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $stores = Store::select('id', 'name', 'address', 'description', 'created_at')->get();

        return response($stores, 200);
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
        $store = Store::select('id', 'name', 'address', 'description', 'created_at')->find($id);

        return response($store, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
}
