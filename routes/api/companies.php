<?php

use App\Http\Controllers\Company\CompanyCostCategoryController;
use App\Http\Controllers\Company\CompanyCostController;
use Illuminate\Support\Facades\Route;
// Company Cost Categories endpoints
Route::prefix('companies/costs/categories')->middleware('auth:sanctum')->controller(CompanyCostCategoryController::class)->group(function () {
  Route::get('/',  'index');
  Route::post('/', 'store');
});

// Company Costs endpoints
Route::prefix('companies/costs')->middleware('auth:sanctum')->controller(CompanyCostController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:companyCosts_list');
  Route::get('/chart', 'getChartData');
  Route::get('/periods', 'getPeriods');
  Route::get('/{id}', 'show')->middleware('permission:companyCosts_display');
  Route::post('/', 'store')->middleware('permission:companyCosts_insert');
  Route::post('/{id}', 'update')->middleware('permission:companyCosts_update');
  Route::post('/{id}/paymentRegistration', 'paymentRegistration')->middleware('permission:companyCosts_update');
});


