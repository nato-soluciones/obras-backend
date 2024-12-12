<?php

use App\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Route;

Route::prefix('materials')->middleware('auth:sanctum')->controller(MaterialController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::put('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});
