<?php

use App\Http\Controllers\StoreMovementConceptController;
use App\Http\Controllers\StoreMovementController;
use App\Http\Controllers\StoreMovementStatusController;
use App\Http\Controllers\StoreMovementTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('store-movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
});

Route::prefix('/store-movement-concepts')->middleware('auth:sanctum')->controller(StoreMovementConceptController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});

Route::prefix('/store-movement-types')->middleware('auth:sanctum')->controller(StoreMovementTypeController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});

Route::prefix('/store-movement-statuses')->middleware('auth:sanctum')->controller(StoreMovementStatusController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});
