<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BudgetTemplateController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractorIndustryController;
use App\Http\Controllers\ObraDailyLogController;
use App\Http\Controllers\ObraDailyLogTagController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\ToolCategoryController;
use App\Http\Controllers\ToolLocationController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ManufacturerCategoryController;
use App\Http\Controllers\ManufacturerFileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentications endpoint
Route::prefix('auth')->controller(AuthController::class)->group(function() {
    Route::post('/login', 'login');
    Route::get('/user', 'user');
});


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Users endpoints
Route::prefix('users')->middleware('auth:sanctum')->controller(UserController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::post('/{id}/password', 'password');
});

// Clients endpoints
Route::prefix('clients')->middleware('auth:sanctum')->controller(ClientController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Budgets endpoints
Route::prefix('budgets')->middleware('auth:sanctum')->controller(BudgetController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/export', 'exportList');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');

    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/revert', 'revert');
});

// Obras endpoints
Route::prefix('obras')->middleware('auth:sanctum')->controller(ObraController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::post('/{id}/documents', 'documents');
    Route::post('/{id}/additionals', 'additionals');
});

Route::prefix('obras/{obraId}/daily_logs')->middleware('auth:sanctum')->controller(ObraDailyLogController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{dailyLogId}', 'show');
    Route::get('/{dailyLogId}/file', 'fileDownload');
    Route::post('/', 'store');
    Route::post('/{dailyLogId}', 'update');
});

// Incomes endpoints
Route::prefix('incomes')->middleware('auth:sanctum')->controller(IncomeController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/export', 'exportList');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Outcomes endpoints
Route::prefix('outcomes')->middleware('auth:sanctum')->controller(OutcomeController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/export', 'exportList');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Additionals endpoints
Route::prefix('additionals')->middleware('auth:sanctum')->controller(AdditionalController::class)->group(function() {
    Route::get('/{id}', 'show');
    Route::post('/{id}', 'update');
});

// Notes endpoints
Route::prefix('notes')->middleware('auth:sanctum')->controller(NoteController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Contractors endpoints
Route::prefix('contractors')->middleware('auth:sanctum')->controller(ContractorController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/export', 'exportList');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Contacts endpoints
Route::prefix('contacts')->middleware('auth:sanctum')->controller(ContactController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Tools endpoints
Route::prefix('tools')->middleware('auth:sanctum')->group(function() {
    Route::get('/categories', [ToolCategoryController::class, 'index']);
    Route::post('/categories', [ToolCategoryController::class, 'store']);
    Route::post('/categories/{id}', [ToolCategoryController::class, 'destroy']);
    Route::post('/locations', [ToolLocationController::class, 'store']);

    Route::get('/', [ToolController::class, 'index']);
    Route::get('/{id}', [ToolController::class, 'show']);
    Route::post('/', [ToolController::class, 'store']);
    Route::post('/{id}', [ToolController::class, 'update']);
    Route::delete('/{id}', [ToolController::class, 'destroy']);
});

// Manufacturies endpoints
Route::prefix('manufacturies')->middleware('auth:sanctum')->group(function() {
    Route::get('/categories', [ManufacturerCategoryController::class, 'index']);
    Route::post('/categories', [ManufacturerCategoryController::class, 'store']);
    Route::post('/categories/{id}', [ManufacturerCategoryController::class, 'destroy']);
    
    Route::post('/files', [ManufacturerFileController::class, 'store']);
    Route::delete('/files/{id}', [ManufacturerFileController::class, 'destroy']);

    Route::get('/', [ManufacturerController::class, 'index']);
    Route::get('/{id}', [ManufacturerController::class, 'show']);
    Route::post('/', [ManufacturerController::class, 'store']);
    Route::post('/{id}', [ManufacturerController::class, 'update']);
    Route::delete('/{id}', [ManufacturerController::class, 'destroy']);
});

// BudgetTemplate endpoints
Route::prefix('budget_templates')->middleware('auth:sanctum')->controller(BudgetTemplateController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// ContractorIndustry endpoints
Route::prefix('contractor_industries')->middleware('auth:sanctum')->controller(ContractorIndustryController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{code}', 'show');
});

// ObraDailyLogTag endpoints
Route::prefix('obra_daily_log_tags')->middleware('auth:sanctum')->controller(ObraDailyLogTagController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});

Route::prefix('banks')->middleware('auth:sanctum')->controller(BankController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{code}', 'show');
});