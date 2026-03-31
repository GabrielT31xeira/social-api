<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthenticateService
{
    public function register(array $data): User
    {
        $avatar = $data['avatar'] ?? null;
        $avatarPath = null;

        try {
            return DB::transaction(function () use ($data, $avatar, &$avatarPath) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'char_name' => $data['char_name'],
                    'password' => Hash::make($data['password']),
                ]);

                if ($avatar instanceof UploadedFile) {
                    $avatarPath = $this->storeAvatarFile($user, $avatar);

                    $user->forceFill([
                        'avatar_path' => $avatarPath,
                    ])->save();
                }

                return $user->refresh();
            });
        } catch (\Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }

            throw $exception;
        }
    }

    public function login(array $data): array
    {
        $user = User::where('char_name', $data['char_name'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'char_name' => [__('auth.login_failed')],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function refreshToken(User $user): string
    {
        $user->currentAccessToken()->delete();

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function updateAvatar(User $user, UploadedFile $avatar): User
    {
        $oldAvatarPath = $user->avatar_path;
        $newAvatarPath = null;

        try {
            $newAvatarPath = $this->storeAvatarFile($user, $avatar);

            $user->forceFill([
                'avatar_path' => $newAvatarPath,
            ])->save();
        } catch (\Throwable $exception) {
            if ($newAvatarPath) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            throw $exception;
        }

        if ($oldAvatarPath) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        return $user->refresh();
    }

    public function removeAvatar(User $user): User
    {
        $avatarPath = $user->avatar_path;

        if (!$avatarPath) {
            return $user;
        }

        $user->forceFill([
            'avatar_path' => null,
        ])->save();

        Storage::disk('public')->delete($avatarPath);

        return $user->refresh();
    }
    private function storeAvatarFile(User $user, UploadedFile $avatar): string
    {
        return $avatar->store('avatars/'.$user->id, 'public');
    }
}
