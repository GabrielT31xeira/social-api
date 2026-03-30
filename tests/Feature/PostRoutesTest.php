<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_delete_own_post_with_english_message(): void
    {
        $user = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $post = Post::query()->create([
            'title' => 'Owned post',
            'content' => 'Owned content',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->deleteJson('/api/posts/'.$post->id.'/destroy');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Post deleted!');

        $this->assertDatabaseMissing('post', [
            'id' => $post->id,
        ]);
    }

    public function test_user_cannot_delete_a_post_from_another_user(): void
    {
        $owner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $intruder = User::factory()->create([
            'char_name' => 'intruder-user',
        ]);

        $post = Post::query()->create([
            'title' => 'Protected post',
            'content' => 'Protected content',
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($intruder);

        $response = $this
            ->withHeader('Accept-Language', 'pt-BR')
            ->deleteJson('/api/posts/'.$post->id.'/destroy');

        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'Post nao encontrado.');

        $this->assertDatabaseHas('post', [
            'id' => $post->id,
        ]);
    }
}
