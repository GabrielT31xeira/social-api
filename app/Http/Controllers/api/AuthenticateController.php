<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\auth\LoginRequest;
use App\Http\Requests\api\auth\UserRequest;
use App\Services\AuthenticateService;
use Illuminate\Http\Request;

class AuthenticateController extends Controller
{
    protected $authService;

    public function __construct(AuthenticateService $authService)
    {
        $this->authService = $authService;
    }


    public function register(UserRequest $request)
    {
        return ApiResponse::success($this->authService->register($request), "Usuario registrado");
    }


    public function login(LoginRequest $request)
    {
        return ApiResponse::success($this->authService->login($request));
    }


    public function refresh()
    {
        return ApiResponse::success($this->authService->refreshToken());
    }


    public function logout()
    {
        return ApiResponse::success($this->authService->logout());
    }
}
