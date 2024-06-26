<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaterialController extends Controller
{
    public function index(): Response
    {
        $materials = Material::select(['id', 'name'])->orderBy('name')->get();
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
    public function show(int $id): Response
    {
        $material = Material::find($id);
        return response($material, 200);
    }

    /**
     * Update a manufacturer category by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $material = Material::find($id);
        $material->update($request->all());
        return response($material, 200);
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
