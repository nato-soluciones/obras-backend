<?php

use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ObraDailyLogController;
use App\Http\Controllers\ObraDailyLogTagController;
use App\Http\Controllers\OutcomeController;
use Illuminate\Support\Facades\Route;


// Obras endpoints
Route::prefix('obras')->middleware('auth:sanctum')->controller(ObraController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
  Route::post('/{id}/documents', 'documents');
  Route::post('/{id}/additionals', 'additionals');
});

// Incomes endpoints
Route::prefix('incomes')->middleware('auth:sanctum')->controller(IncomeController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/export', 'exportList');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});

// Outcomes endpoints
Route::prefix('outcomes')->middleware('auth:sanctum')->controller(OutcomeController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/export', 'exportList');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});

// Additionals endpoints
Route::prefix('additionals')->middleware('auth:sanctum')->controller(AdditionalController::class)->group(function () {
  Route::get('/{id}', 'show');
  Route::post('/{id}', 'update');
});

Route::prefix('obras/{obraId}/daily_logs')->middleware('auth:sanctum')->controller(ObraDailyLogController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{dailyLogId}', 'show');
  Route::get('/{dailyLogId}/file', 'fileDownload');
  Route::post('/', 'store');
  Route::post('/{dailyLogId}', 'update');
});

// ObraDailyLogTag endpoints
Route::prefix('obra_daily_log_tags')->middleware('auth:sanctum')->controller(ObraDailyLogTagController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});