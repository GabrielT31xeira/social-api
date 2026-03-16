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
        return $this->authService->register($request);
    }


    public function login(LoginRequest $request)
    {
        return $this->authService->login($request);
    }


    public function refresh()
    {
        return $this->authService->refreshToken();
    }


    public function logout()
    {
        return $this->authService->logout();
    }
}
