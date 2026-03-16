<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Services\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

            return [
                'user' => $user,
            ];
        } catch (\Exception $exception) {
            return ApiResponse::error();
        }
    }

    public function login($credentials)
    {
        try {
            $user = User::where('char_name', $credentials['char_name'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::unauthorized();
            }
//            dd($user);

            $token = $user->createToken('auth_token')->plainTextToken();

            return [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        } catch (\Exception $exception) {
            return ApiResponse::error();
        }
    }

    public function refreshToken()
    {
        try {
            $user = auth()->user();
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        } catch (\Exception $exception) {
            return ApiResponse::error();
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            $user->tokens()->delete();
            $user->currentAccessToken()->delete();

            return [
                'message' => 'Logout realizado com sucesso'
            ];
        } catch (\Exception $exception) {
            return ApiResponse::error();
        }
    }
}
