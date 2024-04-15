<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\ToolCategoryController;
use App\Http\Controllers\ToolLocationController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ManufacturerCategoryController;
use App\Http\Controllers\ManufacturerFileController;
use App\Http\Controllers\CacController;

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

include_once __DIR__ . '/api/auxiliaries.php';
include_once __DIR__ . '/api/budgets.php';
include_once __DIR__ . '/api/contractors.php';
include_once __DIR__ . '/api/obras.php';

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

// Notes endpoints
Route::prefix('notes')->middleware('auth:sanctum')->controller(NoteController::class)->group(function() {
    Route::get('/', 'index');
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

// CAC endpoints
Route::prefix('cac')->middleware('auth:sanctum')->controller(CacController::class)->group(function() {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy');
});