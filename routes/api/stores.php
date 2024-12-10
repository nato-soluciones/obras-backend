<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('stores')->middleware('auth:sanctum')->controller(StoreController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});
