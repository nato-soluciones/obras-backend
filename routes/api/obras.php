<?php

use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ObraDailyLogController;
use App\Http\Controllers\ObraDailyLogTagController;
use App\Http\Controllers\ObraStageController;
use App\Http\Controllers\ObraStageTaskController;
use App\Http\Controllers\OutcomeController;
use Illuminate\Support\Facades\Route;


// Obras endpoints
Route::prefix('obras')->middleware('auth:sanctum')->controller(ObraController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obras_list');
  Route::get('/{id}', 'show')->middleware('permission:obras_display');
  Route::get('/{id}/contractors', 'contractors')->middleware('permission:obraContractors_list');
  Route::post('/', 'store')->middleware('permission:obras_insert');
  Route::post('/{id}', 'update')->middleware('permission:obras_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obras_delete');
  Route::post('/{id}/documents', 'documents')->middleware('permission:obraDocuments_insert');
  Route::post('/{id}/additionals', 'additionals')->middleware('permission:obraAdditional_insert');
});

// Incomes endpoints
Route::prefix('incomes')->middleware('auth:sanctum')->controller(IncomeController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraIncomes_list');
  Route::get('/export', 'exportList')->middleware('permission:obraIncomes_export');
  Route::get('/{id}', 'show')->middleware('permission:obraIncomes_display');
  Route::post('/', 'store')->middleware('permission:obraIncomes_insert');
  Route::post('/{id}', 'update')->middleware('permission:obraIncomes_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraIncomes_delete');
});

// Outcomes endpoints
Route::prefix('outcomes')->middleware('auth:sanctum')->controller(OutcomeController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraOutcomes_list');
  Route::get('/export', 'exportList')->middleware('permission:obraOutcomes_export');
  Route::get('/{id}', 'show')->middleware('permission:obraOutcomes_display');
  Route::post('/', 'store')->middleware('permission:obraOutcomes_insert');
  Route::post('/{id}', 'update')->middleware('permission:obraOutcomes_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraOutcomes_delete');
});

// Additionals endpoints
Route::prefix('additionals')->middleware('auth:sanctum')->controller(AdditionalController::class)->group(function () {
  Route::get('/{id}', 'show')->middleware('permission:obraAdditional_display');
  Route::post('/{id}', 'update')->middleware('permission:obraAdditional_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraAdditional_delete');
});

// ObraDailyLog endpoints
Route::prefix('obras/{obraId}/daily_logs')->middleware('auth:sanctum')->controller(ObraDailyLogController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obrasDailyLogs_list');
  Route::get('/{dailyLogId}', 'show');
  Route::get('/{dailyLogId}/file', 'fileDownload');
  Route::post('/', 'store')->middleware('permission:obrasDailyLogs_insert');
  Route::post('/{dailyLogId}', 'update')->middleware('permission:obrasDailyLogs_update');
});

// ObraDailyLogTag endpoints
Route::prefix('obra_daily_log_tags')->middleware('auth:sanctum')->controller(ObraDailyLogTagController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}', 'show');
});

// Tasks in stages endpoints
Route::prefix('obras/{obraId}/stages/{stageId}/tasks')->middleware('auth:sanctum')->controller(ObraStageTaskController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraStageTasks_list');
  Route::get('/{taskId}', 'show')->middleware('permission:obraStageTasks_display');
  Route::post('/', 'store')->middleware('permission:obraStageTasks_insert');
  Route::post('/{taskId}', 'update')->middleware('permission:obraStageTasks_update');
  Route::post('/{taskId}/completed', 'checkCompleted');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraStageTasks_delete');
});

// Stages endpoints
Route::prefix('obras/{obraId}/stages')->middleware('auth:sanctum')->controller(ObraStageController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraStages_list');
  Route::get('/{stageId}', 'show')->middleware('permission:obraStages_display');
  Route::post('/', 'store')->middleware('permission:obraStages_insert');
  Route::post('/{stageId}', 'update')->middleware('permission:obraStages_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraStages_delete');
});
