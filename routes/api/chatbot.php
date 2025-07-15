<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Budgets endpoints
Route::prefix('chat')->middleware('auth:sanctum')->controller(ChatController::class)->group(function () {
    Route::post('/message', 'message')->middleware('permission:chat_message');
});
