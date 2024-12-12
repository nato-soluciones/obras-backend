<?php

use App\Http\Controllers\MovementController;
use Illuminate\Support\Facades\Route;

Route::prefix('movements')->middleware('auth:sanctum')->controller(MovementController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
});
