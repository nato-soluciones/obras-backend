<?php

use App\Http\Controllers\StoreMovementConceptController;
use App\Http\Controllers\StoreMovementController;
use App\Http\Controllers\StoreMovementStatusController;
use App\Http\Controllers\StoreMovementTypeController;
use App\Http\Controllers\StoreMovementReasonController;
use Illuminate\Support\Facades\Route;

Route::prefix('store_movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:storeMovement_list');
  Route::get('/{id}', 'show')->middleware('permission:storeMovement_display');

  Route::post('/validate', 'validateTransfer')->middleware('permission:storeMovement_insert');
  Route::post('/validate_output', 'validateOutput')->middleware('permission:storeMovement_insert');
  Route::post('/input', 'storeInput')->middleware('permission:storeMovement_insert');
  Route::post('/output', 'storeOutput')->middleware('permission:storeMovement_insert');
  Route::post('/', 'store')->middleware('permission:storeMovement_insert');

  Route::post('/{id}/accept', 'acceptTransfer')->middleware('permission:storeMovement_approve');
  Route::post('/{id}/reject', 'rejectTransfer')->middleware('permission:storeMovement_approve');
  Route::post('/{id}/cancel', 'cancelTransfer')->middleware('permission:storeMovement_approve');
  Route::post('/{id}/reject_with_adjustment', 'rejectTransferWithAdjustment')
      ->middleware('permission:storeMovement_approve');
});

Route::prefix('stores/{storeId}/movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'indexByStore')->middleware('permission:storeMovement_list');
});

Route::prefix('stores/{storeId}/material/{materialId}/movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'indexByMaterialStore');
});

Route::prefix('/store_movement_concepts')->middleware('auth:sanctum')->controller(StoreMovementConceptController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});

Route::prefix('/store_movement_types')->middleware('auth:sanctum')->controller(StoreMovementTypeController::class)->group(function () {
  Route::get('/', 'indexWithConcepts');
  Route::get('/{id}', 'show');
});

Route::prefix('/store_movement_reasons')->middleware('auth:sanctum')->controller(StoreMovementReasonController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});
