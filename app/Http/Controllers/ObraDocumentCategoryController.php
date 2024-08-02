<?php

namespace App\Http\Controllers;

use App\Models\ObraDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ObraDocumentCategoryController extends Controller
{
    public function index(): Response
    {
        $documentCategories = ObraDocumentCategory::select(['id', 'name'])->orderBy('name')->get();
        return response($documentCategories, 200);
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

        // Verificar si ya existe una categoría con el mismo nombre (sin importar mayúsculas/minúsculas)
        $exists = ObraDocumentCategory::whereRaw('LOWER(name) = ?', [$name])->exists();

        if ($exists) {
            return response(['message' => 'Ya existe una categoría con este nombre'], 201);
        }
        $documentCategory = ObraDocumentCategory::create($request->all());
        return response($documentCategory, 201);
    }

    /**
     * Get a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $categoryId): Response
    {
        $documentCategory = ObraDocumentCategory::find($categoryId);
        return response($documentCategory, 200);
    }

    /**
     * Update a manufacturer category by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $categoryId): Response
    {
        $documentCategory = ObraDocumentCategory::find($categoryId);
        $documentCategory->update($request->all());
        return response($documentCategory, 200);
    }

    /**
     * Delete a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $categoryId): Response
    {
        $documentCategory = ObraDocumentCategory::find($categoryId);
        $documentCategory->delete();
        return response(null, 204);
    }
}
