<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OAuthPkceController;
use App\Http\Controllers\Api\V1\OAuthSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/oauth/session', OAuthSessionController::class);
    Route::post('/oauth/pkce/token', [OAuthPkceController::class, 'exchange']);

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login/password', [AuthController::class, 'loginWithPassword']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
});
