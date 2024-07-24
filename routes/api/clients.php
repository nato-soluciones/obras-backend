<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CurrentAccountController;
use App\Http\Controllers\CurrentAccountMovementController;

// Clients endpoints
Route::prefix('clients')->middleware('auth:sanctum')->controller(ClientController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:clients_list');
  Route::get('/{id}', 'show')->middleware('permission:clients_display');
  Route::post('/', 'store')->middleware('permission:clients_insert');
  Route::post('/{id}', 'update')->middleware('permission:clients_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:clients_delete');
});

// CurrentAccounts Clients endpoints
Route::prefix('clients/{id}/curr_accs')->middleware('auth:sanctum')->controller(CurrentAccountController::class)->group(function () {
  Route::get('/', 'indexClients')->middleware('permission:clientCurrentAccounts_list');
  Route::get('/{projectId}/{currency}', 'showClient')->middleware('permission:clientCurrentAccounts_display');
  Route::post('/', 'storeClient')->middleware('permission:clientCurrentAccounts_insert');
});

// CurrentAccountMovements Clients endpoints
Route::prefix('clients/{id}/curr_accs/{projectId}/{currency}/movements')->middleware(['auth:sanctum', 'api'])->controller(CurrentAccountMovementController::class)->group(function () {
  Route::get('/', 'indexClients')->middleware('permission:clientCurrentAccountMovements_list');
  Route::get('/{movementId}', 'showClient')->middleware('permission:clientCurrentAccountMovements_display');
  Route::post('/', 'storeClient')->middleware('permission:clientCurrentAccountMovements_insert');
  Route::post('/{movementId}', 'updateClient')->middleware('permission:clientCurrentAccountMovements_update');
});