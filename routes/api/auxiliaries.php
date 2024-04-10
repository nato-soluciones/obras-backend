<?php

use App\Http\Controllers\BankController;
use Illuminate\Support\Facades\Route;

Route::prefix('banks')->middleware('auth:sanctum')->controller(BankController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{code}', 'show');
});