<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContractorController;

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
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');

    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/revert', 'revert');
    Route::post('/{id}/finish', 'finish');
});

// Obras endpoints
Route::prefix('obras')->middleware('auth:sanctum')->controller(ObraController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

// Incomes endpoints
Route::prefix('incomes')->middleware('auth:sanctum')->controller(IncomeController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
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
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});