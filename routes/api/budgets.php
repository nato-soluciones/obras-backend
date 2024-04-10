<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetTemplateController;
use Illuminate\Support\Facades\Route;

// Budgets endpoints
Route::prefix('budgets')->middleware('auth:sanctum')->controller(BudgetController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/export', 'exportList');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');

  Route::post('/{id}/approve', 'approve');
  Route::post('/{id}/revert', 'revert');
});

// BudgetTemplate endpoints
Route::prefix('budget_templates')->middleware('auth:sanctum')->controller(BudgetTemplateController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::delete('/{id}', 'destroy');
});