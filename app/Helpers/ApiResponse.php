<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function successPaginate($data, array $context = [], string $message = ''): \Illuminate\Http\JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
            'links' => [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ],
        ];

        if ($context !== []) {
            $payload['context'] = $context;
        }

        return response()->json($payload);
    }

    public static function successWithBody($data = null, string $message = ''): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function success(string $message, int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $code);
    }

    public static function error(string $message = 'Server Error', int $code = 500, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    public static function validation($errors)
    {
        $firstError = collect($errors)
            ->flatten()
            ->first();

        return response()->json([
            'success' => false,
            'message' => $firstError,
            'errors' => $errors
        ], 422);
    }

    public static function unauthorized(string $message = 'Unauthorized')
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }
}
