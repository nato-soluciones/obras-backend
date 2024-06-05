<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\ManufacturerCategory;

class ManufacturerCategoryController extends Controller
{
    /**
     * Get all manufacturer categories
     *
     * @return Response
     */
    public function index(): Response
    {
        $manufacturerCategories = ManufacturerCategory::select(['id', 'name'])->orderBy('name')->get();
        return response($manufacturerCategories, 200);
    }

    /**
     * Create a manufacturer category
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $manufacturerCategory = ManufacturerCategory::create($request->all());
        return response($manufacturerCategory, 201);
    }

    /**
     * Get a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $manufacturerCategory = ManufacturerCategory::find($id);
        return response($manufacturerCategory, 200);
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
        $manufacturerCategory = ManufacturerCategory::find($id);
        $manufacturerCategory->update($request->all());
        return response($manufacturerCategory, 200);
    }

    /**
     * Delete a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $manufacturerCategory = ManufacturerCategory::find($id);
        $manufacturerCategory->delete();
        return response(null, 204);
    }
}
