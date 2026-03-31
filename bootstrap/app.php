<?php

use App\Helpers\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof ValidationException) {
                return ApiResponse::validation($e->errors());
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::unauthorized(__('auth.unauthenticated'));
            }

            if ($e instanceof AuthorizationException) {
                return ApiResponse::error(
                    $e->getMessage() ?: __('errors.forbidden'),
                    403
                );
            }

            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error(__('errors.not_found'), 404);
            }

            if ($e instanceof HttpException) {
                return ApiResponse::error(
                    $e->getMessage() ?: __('errors.http'),
                    $e->getStatusCode()
                );
            }

            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : __('errors.server'),
                500
            );
        });
    })
    ->create();
