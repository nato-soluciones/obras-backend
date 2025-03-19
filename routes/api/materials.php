<?php

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('materials')->middleware('auth:sanctum')->controller(MaterialController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:material_list');
  Route::get('/{id}', 'show')->middleware('permission:material_display');
  Route::post('/', 'store')->middleware('permission:material_insert');
  Route::put('/{id}', 'update')->middleware('permission:material_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:material_delete');
  Route::get('/{id}/stores','getStoresByMaterial')->middleware('permission:material_display');
});

Route::prefix('material_store')->middleware('auth:sanctum')->controller(MaterialStoreController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:material_list');
  Route::post('/', 'store')->middleware('permission:material_insert');
  Route::get('/{id}', 'show')->middleware('permission:material_display');
  Route::put('/{id}', 'update')->middleware('permission:material_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:material_delete');
  Route::post('/limits', 'updateLimits')->middleware('permission:material_update');
});