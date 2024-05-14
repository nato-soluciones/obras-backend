<?php

use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorIndustryController;
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
});