<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthAvatarRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_own_profile(): void
    {
        $user = User::factory()->create([
            'char_name' => 'profile-owner',
        ]);

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get('/api/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', (string) $user->id)
            ->assertJsonPath('data.char_name', 'profile-owner');
    }

    public function test_user_can_register_with_avatar(): void
    {
        Storage::fake('public');

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Accept-Language', 'en')
            ->post('/api/register', [
                'name' => 'Avatar User',
                'char_name' => 'avatar-user',
                'email' => 'avatar@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'avatar' => $this->fakeAvatarUpload('avatar.png'),
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'User registered successfully!')
            ->assertJsonPath('data.char_name', 'avatar-user');

        $user = User::query()->where('char_name', 'avatar-user')->firstOrFail();

        $this->assertNotNull($user->avatar_path);
        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_authenticated_user_can_update_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'char_name' => 'avatar-owner',
        ]);

        Storage::disk('public')->put('avatars/'.$user->id.'/old-avatar.jpg', 'old-avatar');

        $user->forceFill([
            'avatar_path' => 'avatars/'.$user->id.'/old-avatar.jpg',
        ])->save();

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Accept-Language', 'en')
            ->post('/api/me/avatar', [
                'avatar' => $this->fakeAvatarUpload('new-avatar.png'),
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Avatar updated successfully!')
            ->assertJsonPath('data.char_name', 'avatar-owner');

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        $this->assertStringContainsString('avatars/'.$user->id.'/', $user->avatar_path);
        Storage::disk('public')->assertMissing('avatars/'.$user->id.'/old-avatar.jpg');
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_authenticated_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'char_name' => 'avatar-remove',
        ]);

        Storage::disk('public')->put('avatars/'.$user->id.'/avatar.jpg', 'avatar-content');

        $user->forceFill([
            'avatar_path' => 'avatars/'.$user->id.'/avatar.jpg',
        ])->save();

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Accept-Language', 'en')
            ->delete('/api/me/avatar');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Avatar removed successfully!')
            ->assertJsonPath('data.avatar_url', null);

        $user->refresh();

        $this->assertNull($user->avatar_path);
        Storage::disk('public')->assertMissing('avatars/'.$user->id.'/avatar.jpg');
    }

    private function fakeAvatarUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WnPZXcAAAAASUVORK5CYII=')
        );
    }
}
