<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('stores')->middleware('auth:sanctum')->controller(StoreController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:store_list');
  Route::get('/with_materials', 'indexWithMaterials')->middleware('permission:store_list');
  Route::get('/{id}', 'show')->middleware('permission:store_display');
  Route::post('/', 'store')->middleware('permission:store_insert');
  Route::put('/{id}', 'update')->middleware('permission:store_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:store_delete');
  Route::get('/{id}/limits', 'getLimits')->middleware('permission:store_display');
});
