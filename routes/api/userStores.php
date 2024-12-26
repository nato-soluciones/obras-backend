<?php

use App\Http\Controllers\UserStoreController;
use Illuminate\Support\Facades\Route;

Route::apiResource('user-stores', UserStoreController::class); 