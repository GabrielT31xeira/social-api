<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\auth\LoginRequest;
use App\Http\Requests\api\auth\UserRequest;
use App\Services\AuthenticateService;

use App\Http\Resources\UserResource;

class AuthenticateController extends Controller
{
    public function __construct(
        private AuthenticateService $authService
    ) {}

    public function register(UserRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return ApiResponse::successWithBody(
            new UserResource($user),
            __('auth.register_success')
        );
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return ApiResponse::successWithBody([
            'access_token' => $data['token'],
            'token_type' => 'Bearer',
            'user' => new UserResource($data['user']),
        ], __('auth.login_success'));
    }

    public function refreshToken()
    {
        $token = $this->authService->refreshToken();

        return ApiResponse::successWithBody([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], __('auth.refresh_success'));
    }

    public function logout()
    {
        $this->authService->logout();

        return ApiResponse::success(__('auth.logout_success'));
    }
}
