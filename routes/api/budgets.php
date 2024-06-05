<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetTemplateController;
use Illuminate\Support\Facades\Route;

// Budgets endpoints
Route::prefix('budgets')->middleware('auth:sanctum')->controller(BudgetController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:budgets_list');
  Route::get('/export', 'exportList')->middleware('permission:budgets_export');
  Route::get('/{id}', 'show')->middleware('permission:budgets_display');
  Route::post('/', 'store')->middleware('permission:budgets_insert');
  Route::post('/{id}', 'update')->middleware('permission:budgets_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:budgets_delete');

  Route::post('/{id}/approve', 'approve')->middleware('permission:budgets_approve');
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