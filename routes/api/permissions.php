<?php

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolFunctionalController;
use App\Http\Controllers\RolUserController;
use App\Http\Controllers\UserController;

// Authentications endpoint
Route::prefix('auth')->controller(AuthController::class)->group(function () {
  Route::post('/login', 'login');
  Route::get('/user', 'user');
});


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
  return $request->user();
});

// Users endpoints
Route::prefix('users')->middleware('auth:sanctum')->controller(UserController::class)->group(function () {
  Route::get('/', 'index')->middleware('permission:users_list');
  Route::get('/permissions_check', 'permissionsCheck');
  Route::get('/entity_check', 'entityCheck');
  Route::get('/{id}/full', 'showWithPermissions')->middleware('permission:users_display');
  Route::get('/{id}', 'show')->middleware('permission:users_display');
  Route::post('/', 'store')->middleware('permission:users_insert');
  Route::post('/{id}', 'update')->middleware('permission:users_update');
  Route::delete('/{id}', 'destroy')->middleware('permission:users_delete');
  Route::post('/{id}/password', 'password');
});

// Roles de usuario
Route::prefix('role_users')->middleware('auth:sanctum')->controller(RolUserController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}/users', 'usersAssociated');
  Route::get('/{id}/role_functionals', 'functionalRoleAssociated');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::post('/{id}/role_functionals', 'functionalRoleAdd');
  Route::delete('/{id}/role_functionals', 'functionalRoleRemove');
});

// Roles funcionales
Route::prefix('role_functionals')->middleware('auth:sanctum')->controller(RolFunctionalController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('/{id}/role_users', 'userRolesAssociated');
  Route::get('/{id}/permissions', 'permissionsAssociated');
  Route::get('/{id}', 'show');
  Route::post('/', 'store');
  Route::post('/{id}', 'update');
  Route::post('/{id}/permissions', 'permissionsAdd');
  Route::delete('/{id}/permissions', 'permissionsRemove');
});

// Permisos
Route::prefix('permissions')->middleware('auth:sanctum')->controller(PermissionController::class)->group(function () {
  Route::get('/', 'index');
});


