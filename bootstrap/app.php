<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\ApiResponse;

return \Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, $request) {

            // 🔴 Validação
            if ($e instanceof ValidationException) {
                return ApiResponse::validation(
                    $e->errors()
                );
            }

            // 🔴 Não autenticado
            if ($e instanceof AuthenticationException) {
                return ApiResponse::unauthorized(
                    __('auth.unauthenticated')
                );
            }

            // 🔴 Model não encontrado (404)
            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error(
                    __('errors.not_found'),
                    404
                );
            }

            // 🔴 Erros HTTP (403, 404, etc)
            if ($e instanceof HttpException) {
                return ApiResponse::error(
                    $e->getMessage() ?: __('errors.http'),
                    $e->getStatusCode()
                );
            }

            // 🔴 Erro genérico
            return ApiResponse::error(
                config('app.debug')
                    ? $e->getMessage()
                    : __('errors.server'),
                500
            );
        });

    })->create();
