<?php

use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorIndustryController;
use Illuminate\Support\Facades\Route;

// Contractors endpoints
Route::prefix('contractors')->middleware('auth:sanctum')->controller(ContractorController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/export', 'exportList');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});

// ContractorIndustry endpoints
Route::prefix('contractor_industries')->middleware('auth:sanctum')->controller(ContractorIndustryController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{code}', 'show');
});