<?php

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('materials')->middleware('auth:sanctum')->controller(MaterialController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::put('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});

Route::prefix('material_store')->middleware('auth:sanctum')->controller(MaterialStoreController::class)->group(function () {
  Route::get('/', 'index');
  Route::post('/', 'store');
  Route::get('/{id}', 'show');
  Route::put('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
  Route::patch('/{id}/limits', 'updateLimits');
});