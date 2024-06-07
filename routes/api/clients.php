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
  Route::get('/', 'indexClients');
});

// CurrentAccountMovements Clients endpoints
Route::prefix('clients/{id}/curr_accs/{currency}/movements')->middleware('auth:sanctum')->controller(CurrentAccountMovementController::class)->group(function () {
  Route::get('/', 'indexClients');
  Route::get('/{movementId}', 'showClients'); // ->middleware('permission:contractors_display');
  Route::post('/', 'storeClients'); // ->middleware('permission:contractors_insert');
});