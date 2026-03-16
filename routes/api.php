<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\api\AuthenticateController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\api\AuthenticateController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/refresh', [\App\Http\Controllers\api\AuthenticateController::class, 'refresh']);
    Route::post('/logout', [\App\Http\Controllers\api\AuthenticateController::class, 'logout']);

    // ********* POST *********
    Route::post('/posts/store', [\App\Http\Controllers\api\PostController::class, 'store']);
});
