<?php

use App\Http\Controllers\CalendarCategoryController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Calendar Events Routes
Route::group(['prefix' => 'calendar', 'middleware' => 'auth:sanctum'], function () {
    // Events CRUD
    Route::get('events', [CalendarEventController::class, 'index'])->middleware('permission:calendar_list');
    Route::post('events', [CalendarEventController::class, 'store'])->middleware('permission:calendar_insert');
    Route::get('events/{event}', [CalendarEventController::class, 'show'])->middleware('permission:calendar_display');
    Route::put('events/{event}', [CalendarEventController::class, 'update'])->middleware('permission:calendar_update');
    Route::delete('events/{event}', [CalendarEventController::class, 'destroy'])->middleware('permission:calendar_delete');
    
    // Participant status update
    Route::put('events/{event}/participants/{participant}/status', [CalendarEventController::class, 'updateParticipantStatus'])->middleware('permission:calendar_list');
    
    // Categories
    Route::get('categories', [CalendarCategoryController::class, 'index'])->middleware('permission:calendar_list');
});

// User search for participants
Route::get('users/search', [UserController::class, 'search'])->middleware('auth:sanctum');