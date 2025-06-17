<?php

use App\Http\Controllers\Material\MaterialCategoryController;
use App\Http\Controllers\Material\MaterialController;
use App\Http\Controllers\MaterialStoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('materials')->middleware('auth:sanctum')->group(function () {
  Route::get('/categories', [MaterialCategoryController::class, 'index'])->middleware('permission:material_category_list');
  Route::post('/categories', [MaterialCategoryController::class, 'store'])->middleware('permission:material_category_insert');
  Route::delete('/categories/{id}', [MaterialCategoryController::class, 'destroy'])->middleware('permission:material_category_delete');

  Route::get('/', [MaterialController::class, 'index'])->middleware('permission:material_list');
  Route::get('/{id}', [MaterialController::class, 'show'])->middleware('permission:material_display');
  Route::post('/', [MaterialController::class, 'store'])->middleware('permission:material_insert');
  Route::put('/{id}', [MaterialController::class, 'update'])->middleware('permission:material_update');
  Route::delete('/{id}', [MaterialController::class, 'destroy'])->middleware('permission:material_delete');
  Route::get('/{id}/stores', [MaterialController::class, 'getStoresByMaterial'])->middleware('permission:material_display');

});

Route::prefix('material_store')->middleware('auth:sanctum')->controller(MaterialStoreController::class)->group(function () {
  Route::post('/limits', 'updateLimits')->middleware('permission:storeMaterial_updateStockLimits');
});
