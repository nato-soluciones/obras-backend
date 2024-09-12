<?php

use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MyTaskController;
use App\Http\Controllers\MyTaskEventController;
use App\Http\Controllers\Obra\ObraPlanChargeDetailController;
use App\Http\Controllers\Obra\ObraPlanChargeController;
use App\Http\Controllers\ObraAdditionalController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ObraDailyLogController;
use App\Http\Controllers\ObraDailyLogTagController;
use App\Http\Controllers\ObraDocumentCategoryController;
use App\Http\Controllers\ObraDocumentController;
use App\Http\Controllers\ObraMaterialController;
use App\Http\Controllers\ObraMaterialMovementController;
use App\Http\Controllers\ObraStageController;
use App\Http\Controllers\ObraStageSubStageController;
use App\Http\Controllers\ObraStageSubStageTaskController;
use App\Http\Controllers\ObraStageSubStageTaskEventController;
use App\Http\Controllers\OutcomeController;
use Illuminate\Support\Facades\Route;


// Obras endpoints
Route::prefix('obras')->middleware('auth:sanctum')->controller(ObraController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obras_list');
  Route::get('/{id}', 'show')->middleware('permission:obras_display');
  Route::get('/{id}/general-view-totals', 'getGeneralViewTotals')->middleware('permission:obras_display');
  Route::post('/', 'store')->middleware('permission:obras_insert');
  Route::post('/{id}', 'update')->middleware('permission:obras_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obras_delete');
  Route::delete('/{id}/image', 'imageDestroy')->middleware('permission:obras_delete');

  Route::get('/{id}/contractors', 'contractors')->middleware('permission:obraContractors_list');
});

// My Tasks endpoints
Route::prefix('my-tasks/obras')->middleware('auth:sanctum')->controller(MyTaskController::class)->group(function () {
  Route::get('/', 'obrasList')->middleware('permission:myTasks_list');
  Route::get('/{obraId}', 'myTasksInObra')->middleware('permission:myTasks_list');
  Route::post('/{obraId}/update_progress', 'bulkUpdate')->middleware('permission:myTasks_changeProgress');
});

// My Tasks Events endpoints
Route::prefix('my-tasks/obras/{obraId}/tasks/{taskId}/events')->middleware('auth:sanctum')->controller(MyTaskEventController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:myTasks_listEvents');
  Route::post('/', 'store')->middleware('permission:myTasks_insertEvent');
});

// Incomes endpoints
Route::prefix('obras/{obraId}/incomes')->middleware('auth:sanctum')->controller(IncomeController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraIncomes_list');
  Route::get('/list_all', 'listAll')->middleware('permission:obraIncomes_list');
  Route::get('/export', 'exportList')->middleware('permission:obraIncomes_export');
  Route::get('/{incomeId}', 'show')->middleware('permission:obraIncomes_display');
  Route::post('/', 'store')->middleware('permission:obraIncomes_insert');
  Route::post('/{incomeId}', 'update')->middleware('permission:obraIncomes_update');
  Route::delete('/{incomeId}', 'destroy')->middleware('permission:obraIncomes_delete');
});

// Plan Charges endpoints
Route::prefix('obras/{obraId}/plan_charges')->middleware('auth:sanctum')->controller(ObraPlanChargeController::class)->group(function () {
  Route::post('/', 'store')->middleware('permission:obraPlanCharges_insert');
});

// Plan Charge Details endpoints
Route::prefix('obras/{obraId}/plan_charges/details')->middleware('auth:sanctum')->controller(ObraPlanChargeDetailController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraPlanChargeDetails_list');
  Route::get('/index_totals', 'indexTotals')->middleware('permission:obraPlanChargeDetails_list');
  Route::get('/{detailId}', 'index')->middleware('permission:obraPlanChargeDetails_display');
  Route::post('/', 'store')->middleware('permission:obraPlanChargeDetails_insert');
  Route::post('/{detailId}/charge', 'charge')->middleware('permission:obraPlanChargeDetails_charge');
});

// Outcomes endpoints
Route::prefix('obras/{obraId}/outcomes')->middleware('auth:sanctum')->controller(OutcomeController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraOutcomes_list');
  Route::get('/list_all', 'listAll')->middleware('permission:obraOutcomes_list');
  Route::get('/export', 'exportList')->middleware('permission:obraOutcomes_export');
  Route::get('/{outcomeId}', 'show')->middleware('permission:obraOutcomes_display');
  Route::post('/', 'store')->middleware('permission:obraOutcomes_insert');
  Route::post('/{outcomeId}', 'update')->middleware('permission:obraOutcomes_update');
  Route::delete('/{outcomeId}', 'destroy')->middleware('permission:obraOutcomes_delete');
});

// Documents endpoints
Route::prefix('obras/{obraId}/documents')->middleware('auth:sanctum')->controller(ObraDocumentController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraDocuments_list');
  Route::post('/', 'store')->middleware('permission:obraDocuments_insert');
  Route::delete('/{documentId}', 'destroy')->middleware('permission:obraDocuments_delete');
});

// Document Categories endpoints
Route::prefix('obras/documents/categories')->middleware('auth:sanctum')->controller(ObraDocumentCategoryController::class)->group(function () {
  Route::get('/',  'index');
  Route::post('/', 'store');
});

// Additionals endpoints
Route::prefix('obras/{obraId}/additionals')->middleware('auth:sanctum')->controller(ObraAdditionalController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraAdditional_list');
  Route::get('/{id}', 'show')->middleware('permission:obraAdditional_display');
  Route::post('/', 'store')->middleware('permission:obraAdditional_insert');
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

// Task Events endpoints
Route::prefix('obras/{obraId}/stages/{stageId}/sub_stages/{subStageId}/tasks/{taskId}/events')->middleware('auth:sanctum')->controller(ObraStageSubStageTaskEventController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraStageSubStageTasks_listEvents');
});

// Tasks endpoints
Route::prefix('obras/{obraId}/stages/{stageId}/sub_stages/{subStageId}/tasks')->middleware('auth:sanctum')->controller(ObraStageSubStageTaskController::class)->group(function () {
  // Route::get('/', 'index')->middleware('permission:obraStageSubStageTasks_list');
  Route::get('/{taskId}', 'show')->middleware('permission:obraStageSubStageTasks_display');
  Route::post('/', 'store')->middleware('permission:obraStageSubStageTasks_insert');
  Route::post('/{taskId}', 'update')->middleware('permission:obraStageSubStageTasks_update');
  Route::post('/{taskId}/update_progress', 'updateProgress');
  Route::delete('/{taskId}', 'destroy')->middleware('permission:obraStageSubStageTasks_delete');
});

// SubStages endpoints
Route::prefix('obras/{obraId}/stages/{stageId}/sub_stages')->middleware('auth:sanctum')->controller(ObraStageSubStageController::class)->group(function () {
  Route::get('/full', 'indexWithTasks')->middleware('permission:obraStageSubStages_list');
  Route::get('/{subStageId}', 'show')->middleware('permission:obraStageSubStages_display');
  Route::post('/', 'store')->middleware('permission:obraStageSubStages_insert');
  Route::post('/{subStageId}', 'update')->middleware('permission:obraStageSubStages_update');
  Route::delete('/{subStageId}', 'destroy')->middleware('permission:obraStageSubStages_delete');
});

// Stages endpoints
Route::prefix('obras/{obraId}/stages')->middleware('auth:sanctum')->controller(ObraStageController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraStages_list');
  Route::get('/gantt', 'indexGantt')->middleware('permission:obraStages_list');
  Route::get('/{stageId}', 'show')->middleware('permission:obraStages_display');
  Route::post('/', 'store')->middleware('permission:obraStages_insert');
  Route::post('/{stageId}', 'update')->middleware('permission:obraStages_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraStages_delete');
});

// materials endpoints
Route::prefix('obras/{obraId}/materials')->middleware('auth:sanctum')->controller(ObraMaterialController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraMaterials_list');
  Route::get('/{materialId}', 'show')->middleware('permission:obraMaterials_display');
  Route::post('/', 'store')->middleware('permission:obraMaterials_insert');
});

// materials movements endpoints
Route::prefix('obras/{obraId}/materials/{obraMaterialId}/movements')->middleware('auth:sanctum')->controller(ObraMaterialMovementController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:obraMaterials_list');
  Route::get('/{movementId}', 'show')->middleware('permission:obraMaterials_display');
  Route::post('/', 'store')->middleware('permission:obraMaterials_insert');
  Route::post('/{movementId}', 'update')->middleware('permission:obraMaterials_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:obraMaterials_delete');
});
