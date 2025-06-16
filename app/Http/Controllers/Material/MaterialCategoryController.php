<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\MaterialCategory;

class MaterialCategoryController extends Controller
{
    /**
     * Get all categories for tools
     *
     * @return Response
     */
    public function index(): Response
    {
        $categories = MaterialCategory::select(['id', 'name'])->orderBy('name')->get();
        return response($categories, 200);
    }

    /**
     * Store a category for tools
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $name = strtolower($request->input('name'));

        // Verificar si ya existe una categoría con el mismo nombre (sin importar mayúsculas/minúsculas)
        $exists = MaterialCategory::whereRaw('LOWER(name) = ?', [$name])->exists();

        if ($exists) {
            return response(['message' => 'Ya existe una categoría con este nombre'], 201);
        }
        $category = MaterialCategory::create($request->all());
        return response($category, 201);
    }
    
    /**
     * Delete a category for tools
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $category = MaterialCategory::find($id);
        $category->delete();
        return response(null, 204);
    }
}
