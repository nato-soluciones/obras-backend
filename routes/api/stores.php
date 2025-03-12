<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('stores')->middleware('auth:sanctum')->controller(StoreController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:stockStore_list');
  Route::get('/with_materials', 'indexWithMaterials')->middleware('permission:stockStore_list');
  Route::get('/{id}', 'show')->middleware('permission:stockStore_display');
  Route::post('/', 'store')->middleware('permission:stockStore_insert');
  Route::put('/{id}', 'update')->middleware('permission:stockStore_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:stockStore_delete');
  Route::get('/{id}/limits', 'getLimits')->middleware('permission:stockStore_display');
});
