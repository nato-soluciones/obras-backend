<?php

use App\Http\Controllers\StoreMovementConceptController;
use App\Http\Controllers\StoreMovementController;
use App\Http\Controllers\StoreMovementStatusController;
use App\Http\Controllers\StoreMovementTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('store_movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:stockMovement_list');
  Route::get('/{id}', 'show')->middleware('permission:stockMovement_display');
  Route::post('/', 'store')->middleware('permission:stockMovement_create ');
  Route::post('/input', 'storeInput')->middleware('permission:stockMovement_input_create ');
  Route::post('/output', 'storeOutput')->middleware('permission:stockMovement_output_create ');
  Route::post('/{id}/accept', 'acceptTransfer')->middleware('permission:stockMovement_approve');
  Route::post('/{id}/reject', 'rejectTransfer')->middleware('permission:stockMovement_approve');
  Route::post('/{id}/cancel', 'cancelTransfer')->middleware('permission:stockMovement_approve');
  Route::post('/{id}/reject_with_adjustment', 'rejectTransferWithAdjustment')
      ->middleware('permission:stockMovement_approve');
});

Route::prefix('/store_movement_concepts')->middleware('auth:sanctum')->controller(StoreMovementConceptController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:stockMovementConcept_list');
  Route::get('/{id}', 'show')->middleware('permission:stockMovementConcept_display');
});

Route::prefix('/store_movement_types')->middleware('auth:sanctum')->controller(StoreMovementTypeController::class)->group(function () {
  Route::get('/', 'indexWithConcepts')->middleware('permission:stockMovementType_list');
  Route::get('/{id}', 'show')->middleware('permission:stockMovementType_display');
});

Route::prefix('/store_movement_statuses')->middleware('auth:sanctum')->controller(StoreMovementStatusController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:stockMovementStatus_list');
  Route::get('/{id}', 'show')->middleware('permission:stockMovementStatus_display');
});

Route::prefix('stores/{storeId}/movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
    Route::get('/', 'indexByStore')->middleware('permission:stockMovement_store_history_list');
});

Route::prefix('material_store/{materialStoreId}/movements')->middleware('auth:sanctum')->controller(StoreMovementController::class)->group(function () {
  Route::get('/', 'indexByMaterialStore')->middleware('permission:stockMovement_material_store_history_list');
});
