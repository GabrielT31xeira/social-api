<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_comment_with_portuguese_message(): void
    {
        $user = User::factory()->create([
            'char_name' => 'criador-comentario',
        ]);

        $post = Post::query()->create([
            'title' => 'Post de teste',
            'content' => 'Conteudo do post de teste',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept-Language', 'pt-BR')
            ->postJson('/api/comments', [
                'description' => 'Meu comentario de teste',
                'post_id' => $post->id,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Comentário criado com sucesso.')
            ->assertJsonPath('data.description', 'Meu comentario de teste')
            ->assertJsonPath('data.user.char_name', 'criador-comentario');

        $this->assertDatabaseHas('comment', [
            'description' => 'Meu comentario de teste',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_delete_own_comment_with_english_message(): void
    {
        $user = User::factory()->create([
            'char_name' => 'comment-owner',
        ]);

        $post = Post::query()->create([
            'title' => 'Post for delete',
            'content' => 'Delete comment content',
            'user_id' => $user->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Comment to be deleted',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->deleteJson('/api/comments/'.$comment->id);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Comment deleted successfully.');

        $this->assertDatabaseMissing('comment', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_a_comment_from_another_user(): void
    {
        $owner = User::factory()->create([
            'char_name' => 'owner-user',
        ]);

        $intruder = User::factory()->create([
            'char_name' => 'intruder-user',
        ]);

        $post = Post::query()->create([
            'title' => 'Protected post',
            'content' => 'Protected content',
            'user_id' => $owner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Protected comment',
            'post_id' => $post->id,
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($intruder);

        $response = $this
            ->withHeader('Accept-Language', 'pt-BR')
            ->deleteJson('/api/comments/'.$comment->id);

        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'Comentário não encontrado.');

        $this->assertDatabaseHas('comment', [
            'id' => $comment->id,
        ]);
    }
}
