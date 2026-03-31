<?php

use App\Http\Controllers\api\AuthenticateController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthenticateController::class, 'register']);
    Route::post('/login', [AuthenticateController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthenticateController::class, 'me']);
    Route::post('/refresh', [AuthenticateController::class, 'refreshToken'])->middleware('throttle:auth');
    Route::post('/logout', [AuthenticateController::class, 'logout']);
    Route::post('/me/avatar', [AuthenticateController::class, 'updateAvatar']);
    Route::delete('/me/avatar', [AuthenticateController::class, 'removeAvatar']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::get('/users/{user}/posts', [PostController::class, 'getByUser']);

    Route::get('/posts/{post}/comments', [CommentController::class, 'getByPost']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::middleware('throttle:reactions')->group(function () {
        Route::post('/posts/{post}/reactions', [PostController::class, 'react']);
        Route::delete('/posts/{post}/reactions', [PostController::class, 'removeReaction']);
        Route::post('/comments/{comment}/reactions', [CommentController::class, 'react']);
        Route::delete('/comments/{comment}/reactions', [CommentController::class, 'removeReaction']);
    });
});
