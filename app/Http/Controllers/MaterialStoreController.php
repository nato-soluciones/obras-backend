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

    public function updateLimits(Request $request): Response
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.limits' => 'required|array',
            'materials.*.limits.minimum_limit' => 'required|numeric|min:0',
            'materials.*.limits.critical_limit' => 'required|numeric|min:0',
        ]);

        foreach ($validatedData['materials'] as $materialData) {
            // Buscar el material en el almacén
            $storeMaterial = StoreMaterial::where('store_id', $validatedData['store_id'])
                ->where('material_id', $materialData['material_id'])
                ->first();

            if (!$storeMaterial) {
                // Si no existe la relación, crearla con stock inicial 0
                $storeMaterial = StoreMaterial::create([
                    'store_id' => $validatedData['store_id'],
                    'material_id' => $materialData['material_id'],
                    'quantity' => 0,
                    'minimum_limit' => $materialData['limits']['minimum_limit'],
                    'critical_limit' => $materialData['limits']['critical_limit']
                ]);
            } else {
                // Si existe, actualizar los límites
                $storeMaterial->minimum_limit = $materialData['limits']['minimum_limit'];
                $storeMaterial->critical_limit = $materialData['limits']['critical_limit'];
                $storeMaterial->save();
            }
        }

        return response([
            'message' => 'Límites actualizados correctamente'
        ], 200);
    }
} 