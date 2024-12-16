<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MaterialController extends Controller
{
    public function index(): Response
    {
        $materials = Material::with('measurementUnit')->get()->map(function ($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'description' => $material->description,
                'measurement_unit' => $material->measurementUnit
            ];
        });
        
        return response($materials, 200);
    }

    /**
     * Create a manufacturer category
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $name = strtolower($request->input('name'));

        // Verificar si ya existe un material con el mismo nombre (sin importar mayúsculas/minúsculas)
        $exists = Material::whereRaw('LOWER(name) = ?', [$name])->exists();

        if ($exists) {
            return response(['message' => 'Ya existe un material con este nombre'], 201);
        }
        $material = Material::create($request->all());
        return response($material, 201);
    }

    /**
     * Get a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function show(string $id): Response
    {
        $material = Material::with('measurementUnit')->findOrFail($id);
        
        $formatted = [
            'id' => $material->id,
            'name' => $material->name,
            'description' => $material->description,
            'measurement_unit' => $material->measurementUnit
        ];
        
        return response($formatted, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update($request->all());
            return response($material, 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Material no encontrado'], 404);
        }
    }

    /**
     * Delete a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $material = Material::find($id);
        $material->delete();
        return response(null, 204);
    }
}
