<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticateService
{
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'char_name' => $data['char_name'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function login(array $credentials): array
    {
        if (!Auth::attempt([
            'char_name' => $credentials['char_name'],
            'password' => $credentials['password'],
        ])) {
            throw new \Exception(__('auth.login_failed'));
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function refreshToken(): string
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Illuminate\Auth\AuthenticationException();
        }

        $user->currentAccessToken()->delete();

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Illuminate\Auth\AuthenticationException();
        }

        $user->currentAccessToken()->delete();
    }
}
