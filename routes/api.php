<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostcodeController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/tokens', [AuthController::class, 'tokens']);
    Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    Route::delete('/tokens', [AuthController::class, 'revokeAllTokens']);

    // Postcode endpoints
    Route::get('/states', [PostcodeController::class, 'states']);
    Route::get('/cities', [PostcodeController::class, 'cities']);
    Route::get('/postcode/{postcode}', [PostcodeController::class, 'lookup']);
    Route::get('/search', [PostcodeController::class, 'search']);
});
