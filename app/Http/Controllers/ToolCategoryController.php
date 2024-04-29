<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\ToolCategory;

class ToolCategoryController extends Controller
{
    /**
     * Get all categories for tools
     *
     * @return Response
     */
    public function index(): Response
    {
        $categories = ToolCategory::all();
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
        $category = ToolCategory::create($request->all());
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
        $category = ToolCategory::find($id);
        $category->delete();
        return response(null, 204);
    }
}
