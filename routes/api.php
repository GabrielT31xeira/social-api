<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\api\AuthenticateController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\api\AuthenticateController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\api\AuthenticateController::class, 'me']);
    Route::post('/refresh', [\App\Http\Controllers\api\AuthenticateController::class, 'refreshToken']);
    Route::post('/logout', [\App\Http\Controllers\api\AuthenticateController::class, 'logout']);
    Route::post('/me/avatar', [\App\Http\Controllers\api\AuthenticateController::class, 'updateAvatar']);
    Route::delete('/me/avatar', [\App\Http\Controllers\api\AuthenticateController::class, 'removeAvatar']);

    // ********* POST *********
    Route::get('/posts', [\App\Http\Controllers\api\PostController::class, 'index']);
    Route::post('/post/store', [\App\Http\Controllers\api\PostController::class, 'store']);
    Route::delete('/posts/{post_id}/destroy', [\App\Http\Controllers\api\PostController::class, 'destroy']);

    // ********* COMMENT *********
    Route::get('/posts/{post_id}/comments', [\App\Http\Controllers\api\CommentController::class, 'getByPost']);
    Route::post('/comments', [\App\Http\Controllers\api\CommentController::class, 'store']);
    Route::delete('/comments/{comment_id}/destroy', [\App\Http\Controllers\api\CommentController::class, 'destroy']);
});
