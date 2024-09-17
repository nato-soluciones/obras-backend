<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyCostCategory;
use Illuminate\Http\Request;

class CompanyCostCategoryController extends Controller
{
    public function index()
    {
        $companyCostCategories = CompanyCostCategory::select(['id', 'name'])->orderBy('name')->get();
        return response($companyCostCategories, 200);
    }

    public function store(Request $request)
    {
        $name = strtolower($request->input('name'));

        // Verificar si ya existe una categoría con el mismo nombre (sin importar mayúsculas/minúsculas)
        $exists = CompanyCostCategory::whereRaw('LOWER(name) = ?', [$name])->exists();

        if ($exists) {
            return response(['message' => 'Ya existe una categoría con este nombre'], 201);
        }
        $companyCostCategory = CompanyCostCategory::create($request->all());
        return response($companyCostCategory, 201);
    }

}
