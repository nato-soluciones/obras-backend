<?php

use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorIndustryController;
use App\Http\Controllers\CurrentAccountController;
use App\Http\Controllers\CurrentAccountMovementController;
use Illuminate\Support\Facades\Route;

// Contractors endpoints
Route::prefix('contractors')->middleware('auth:sanctum')->controller(ContractorController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:contractors_list');
  Route::get('/export', 'exportList')->middleware('permission:contractors_export');
  Route::get('/{id}', 'show')->middleware('permission:contractors_display');
  Route::post('/', 'store')->middleware('permission:contractors_insert');
  Route::post('/{id}', 'update')->middleware('permission:contractors_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:contractors_delete');
});

// ContractorIndustry endpoints
Route::prefix('contractor_industries')->middleware('auth:sanctum')->controller(ContractorIndustryController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{code}', 'show');
  Route::post('/', 'store');
});

// CurrentAccounts Contractor endpoints
Route::prefix('contractors/{id}/curr_accs')->middleware('auth:sanctum')->controller(CurrentAccountController::class)->group(function () {
  Route::get('/', 'indexProviders')->middleware('permission:providerCurrentAccounts_list');
  Route::get('/{projectId}/{currency}', 'showProvider')->middleware('permission:providerCurrentAccounts_display');
  Route::post('/', 'storeProvider')->middleware('permission:providerCurrentAccounts_insert');
});

// CurrentAccountMovements Contractor endpoints
Route::prefix('contractors/{id}/curr_accs/{projectId}/{currency}/movements')->middleware('auth:sanctum')->controller(CurrentAccountMovementController::class)->group(function () {
  Route::get('/', 'indexProviders')->middleware('permission:providerCurrentAccountMovements_list');
  Route::get('/{movementId}', 'showProvider')->middleware('permission:providerCurrentAccountMovements_display');
  Route::post('/', 'storeProvider')->middleware('permission:providerCurrentAccountMovements_insert');
  Route::post('/{movementId}', 'updateProvider')->middleware('permission:providerCurrentAccountMovements_update');
});