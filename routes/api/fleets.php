<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FleetMovementController;
use App\Http\Controllers\FleetDocumentController;

// Fleet endpoints
Route::prefix('fleets')->middleware('auth:sanctum')->controller(FleetController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:fleets_list');
  Route::get('/{id}', 'show')->middleware('permission:fleets_display');
  Route::post('/', 'store')->middleware('permission:fleets_insert');
  Route::post('/{id}', 'update')->middleware('permission:fleets_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:fleets_delete');
});

// Fleet Movement endpoints
Route::prefix('fleets/{fleet_id}/movements')->middleware('auth:sanctum')->controller(FleetMovementController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:fleets_list');
  Route::get('/{id}', 'show')->middleware('permission:fleets_display');
  Route::post('/', 'store')->middleware('permission:fleets_insert');
  Route::post('/{id}', 'update')->middleware('permission:fleets_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:fleets_delete');
});

// Fleet Document endpoints
Route::prefix('fleets/{fleet_id}/documents')->middleware('auth:sanctum')->controller(FleetDocumentController::class)->group(function () {
  Route::post('/', 'store')->middleware('permission:fleets_insert');
  Route::delete('/{id}', 'destroy')->middleware('permission:fleets_delete');
});