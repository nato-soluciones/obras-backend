<?php

use App\Http\Controllers\Auxiliaries\IndexTypeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CurrentAccountMovementTypeController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\QualityControl\QualityControlTemplateController;
use Illuminate\Support\Facades\Route;

Route::prefix('banks')->middleware('auth:sanctum')->controller(BankController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{code}', 'show');
});

Route::prefix('measurement_units')->middleware('auth:sanctum')->controller(MeasurementUnitController::class)->group(function () {
  Route::get('/', 'index');
});

Route::prefix('current_account_movement_types')->middleware('auth:sanctum')->controller(CurrentAccountMovementTypeController::class)->group(function () {
  Route::get('/', 'index');
});

Route::prefix('index_types')->middleware('auth:sanctum')->controller(IndexTypeController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{indexTypeCode}/periods', 'getPeriods');
});

Route::prefix('quality_control_templates')->middleware('auth:sanctum')->controller(QualityControlTemplateController::class)->group(function () {
  Route::get('/', 'index');
  Route::post('/', 'store');
});