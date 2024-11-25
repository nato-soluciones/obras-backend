<?php

use App\Http\Controllers\AppSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\ToolCategoryController;
use App\Http\Controllers\ToolLocationController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ManufacturerCategoryController;
use App\Http\Controllers\ManufacturerFileController;
use App\Http\Controllers\CacController;
use App\Http\Controllers\IpcController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\InitialSettingController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\NotificationController;

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

include_once __DIR__ . '/api/auxiliaries.php';
include_once __DIR__ . '/api/budgets.php';
include_once __DIR__ . '/api/clients.php';
include_once __DIR__ . '/api/contractors.php';
include_once __DIR__ . '/api/obras.php';
include_once __DIR__ . '/api/fleets.php';
include_once __DIR__ . '/api/permissions.php';
include_once __DIR__ . '/api/companies.php';

Route::prefix('initial_settings')->middleware('auth:sanctum')->controller(InitialSettingController::class)->group(function () {
    Route::get('/', 'index');
});

// Notes endpoints
Route::prefix('notes')->middleware('auth:sanctum')->controller(NoteController::class)->group(function () {
    Route::get('/', 'index')->middleware('permission:notes_list');
    Route::get('/{id}', 'show')->middleware('permission:notes_display');
    Route::post('/', 'store')->middleware('permission:notes_insert');
    Route::post('/{id}', 'update')->middleware('permission:notes_update');
    Route::delete('/{id}', 'destroy')->middleware('permission:notes_delete');
});

// Contacts endpoints
Route::prefix('contacts')->middleware('auth:sanctum')->controller(ContactController::class)->group(function () {
    Route::get('/', 'index')->middleware('permission:contacts_list');
    Route::get('/{id}', 'show')->middleware('permission:contacts_display');
    Route::post('/', 'store')->middleware('permission:contacts_insert');
    Route::post('/{id}', 'update')->middleware('permission:contacts_update');
    Route::delete('/{id}', 'destroy')->middleware('permission:contacts_delete');
});

// Tools Locations endpoints
Route::prefix('tools/{id}/locations')->middleware('auth:sanctum')->controller(ToolLocationController::class)->group(function () {
    Route::post('/', 'store'); //->middleware('permission:locations_insert');
    Route::post('/{locationId}', 'update'); //->middleware('permission:locations_update');
    Route::delete('/{locationId}', 'destroy'); //->middleware('permission:locations_delete');
});

// Tools endpoints
Route::prefix('tools')->middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [ToolCategoryController::class, 'index']);
    Route::post('/categories', [ToolCategoryController::class, 'store']);
    Route::post('/categories/{id}', [ToolCategoryController::class, 'destroy']);

    Route::get('/', [ToolController::class, 'index'])->middleware('permission:tools_list');
    Route::get('/{id}', [ToolController::class, 'show'])->middleware('permission:tools_display');
    Route::post('/', [ToolController::class, 'store'])->middleware('permission:tools_insert');
    Route::post('/{id}', [ToolController::class, 'update'])->middleware('permission:tools_update');
    Route::delete('/{id}', [ToolController::class, 'destroy'])->middleware('permission:tools_delete');
});



// Manufacturies endpoints
Route::prefix('manufacturies')->middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [ManufacturerCategoryController::class, 'index']);
    Route::post('/categories', [ManufacturerCategoryController::class, 'store']);
    // Route::post('/categories/{id}', [ManufacturerCategoryController::class, 'destroy']);

    Route::get('/', [ManufacturerController::class, 'index'])->middleware('permission:manufacturing_list');
    Route::get('/{id}', [ManufacturerController::class, 'show'])->middleware('permission:manufacturing_display');
    Route::post('/', [ManufacturerController::class, 'store'])->middleware('permission:manufacturing_insert');
    Route::post('/{id}', [ManufacturerController::class, 'update'])->middleware('permission:manufacturing_update');
    Route::delete('/{id}', [ManufacturerController::class, 'destroy'])->middleware('permission:manufacturing_delete');
});

// Manufacturies documents endpoints
Route::prefix('manufacturies/{id}/files')->middleware('auth:sanctum')->controller(ManufacturerFileController::class)->group(function () {
    Route::post('/', 'store');
    Route::delete('/{fileId}', 'destroy');
});

// CAC endpoints
Route::prefix('cac')->middleware('auth:sanctum')->controller(CacController::class)->group(function () {
    Route::get('/', 'index')->middleware('permission:indexCAC_list');
    Route::post('/', 'store')->middleware('permission:indexCAC_insert');
    Route::post('/{id}', 'update')->middleware('permission:indexCAC_update');
    Route::delete('/{id}', 'destroy')->middleware('permission:indexCAC_delete');
});

Route::prefix('ipc')->middleware('auth:sanctum')->controller(IpcController::class)->group(function () {
    Route::get('/', 'index')->middleware('permission:indexIPC_list');
    Route::post('/', 'store')->middleware('permission:indexIPC_insert');
    Route::post('/{id}', 'update')->middleware('permission:indexIPC_update');
    Route::delete('/{id}', 'destroy')->middleware('permission:indexIPC_delete');
});

Route::prefix('notifications')->middleware('auth:sanctum')->controller(NotificationController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/newCount', 'notificationNewCount');
    Route::get('/{id}', 'show');
    Route::post('/mark_all_as_read', 'markAllAsRead');
    Route::post('/{id}/mark_as_read', 'markAsRead');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('materials')->middleware('auth:sanctum')->controller(MaterialController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
});

Route::prefix('dashboard')->middleware('auth:sanctum')->controller(DashboardController::class)->group(function () {
    Route::get('/', 'index');
});


Route::prefix('app_settings')->middleware('auth:sanctum')->controller(AppSettingController::class)->group(function () {
    Route::get('/{module}', 'getSettingsByModule');
    Route::post('/{module}', 'getSettingsByKeys');
});
// Route::prefix('developer')->middleware('auth:sanctum')->controller(DeveloperController::class)->group(function() {
//     Route::get('/', 'index');
// });