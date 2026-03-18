<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateService
{
    public function register($data)
    {
        try {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'char_name' => $data->char_name,
                'password' => Hash::make($data->password)
            ]);

            return ApiResponse::success($user, __('auth.register_success'));
        } catch (\Exception $exception) {
            return ApiResponse::error(__('auth.register_error'));
        }
    }

    public function login($credentials)
    {
        try {
            $user = User::where('char_name', $credentials['char_name'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::unauthorized(__('auth.login_failed'));
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(['Bearer' => $token, 'user'=> $user], __('auth.login_success'));
        } catch (\Exception $exception) {
            return ApiResponse::error(__('auth.login_error'));
        }
    }

    public function refreshToken()
    {
        try {
            $user = auth()->user();
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(['Bearer' => $token], __('auth.refresh_success'));
        } catch (\Exception $exception) {
            return ApiResponse::error(__('auth.refresh_error'));
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            $user->tokens()->delete();
            $user->currentAccessToken()->delete();

            return ApiResponse::success(null, __('auth.logout_success'));
        } catch (\Exception $exception) {
            return ApiResponse::error(__('auth.logout_error'));
        }
    }
}
