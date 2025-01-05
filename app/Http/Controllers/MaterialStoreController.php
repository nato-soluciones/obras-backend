<?php

namespace App\Http\Controllers;

use App\Models\StoreMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\MaterialStore\StoreMaterialRequest;
use App\Http\Requests\MaterialStore\UpdateMaterialStoreLimitsRequest;

class MaterialStoreController extends Controller
{
    public function index(): Response
    {
        $materialsStore = StoreMaterial::all();
        return response($materialsStore, 200);
    }

    public function store(StoreMaterialRequest $request): Response
    {
        $materialStore = StoreMaterial::create($request->validated());
        return response($materialStore, 201);
    }

    public function show(int $id): Response
    {
        $materialStore = StoreMaterial::findOrFail($id);
        return response($materialStore, 200);
    }

    public function update(Request $request, int $id): Response
    {
        $materialStore = StoreMaterial::findOrFail($id);
        $materialStore->update($request->all());
        return response($materialStore, 200);
    }

    public function destroy(int $id): Response
    {
        $materialStore = StoreMaterial::findOrFail($id);
        $materialStore->delete();
        return response(null, 204);
    }

    public function updateLimits(UpdateMaterialStoreLimitsRequest $request, int $id): Response
    {
        $materialStore = StoreMaterial::findOrFail($id);
        $materialStore->update($request->validated());
        return response($materialStore, 200);
    }
} 