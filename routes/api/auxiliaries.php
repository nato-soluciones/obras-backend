<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\CurrentAccountMovementTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('banks')->middleware('auth:sanctum')->controller(BankController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{code}', 'show');
});

Route::prefix('current_account_movement_types')->middleware('auth:sanctum')->controller(CurrentAccountMovementTypeController::class)->group(function () {
  Route::get('/', 'index');
});