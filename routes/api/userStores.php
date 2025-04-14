<?php

use App\Http\Controllers\UserStoreController;
use Illuminate\Support\Facades\Route;

Route::apiResource('user_stores', UserStoreController::class); 