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
            ->postJson('/api/posts/'.$post->id.'/comments', [
                'description' => 'Meu comentario de teste',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Comentario criado com sucesso.')
            ->assertJsonPath('data.description', 'Meu comentario de teste')
            ->assertJsonPath('data.user.char_name', 'criador-comentario')
            ->assertJsonPath('data.likes_count', 0)
            ->assertJsonPath('data.dislikes_count', 0)
            ->assertJsonPath('data.my_reaction', null);

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

    public function test_authenticated_user_can_like_a_comment(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $postOwner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Reactable comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($viewer);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->postJson('/api/comments/'.$comment->id.'/reactions', [
                'type' => 'like',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Comment reaction saved successfully.')
            ->assertJsonPath('data.likes_count', 1)
            ->assertJsonPath('data.dislikes_count', 0)
            ->assertJsonPath('data.my_reaction', 'like');

        $this->assertDatabaseHas('comment_reaction', [
            'comment_id' => $comment->id,
            'user_id' => $viewer->id,
            'type' => 'like',
        ]);
    }

    public function test_authenticated_user_can_change_comment_reaction_to_dislike(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $postOwner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Reactable comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($viewer);

        $this->postJson('/api/comments/'.$comment->id.'/reactions', [
            'type' => 'like',
        ]);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->postJson('/api/comments/'.$comment->id.'/reactions', [
                'type' => 'dislike',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.likes_count', 0)
            ->assertJsonPath('data.dislikes_count', 1)
            ->assertJsonPath('data.my_reaction', 'dislike');
    }

    public function test_authenticated_user_can_remove_reaction_from_comment(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $postOwner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Reactable comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($viewer);

        $this->postJson('/api/comments/'.$comment->id.'/reactions', [
            'type' => 'dislike',
        ]);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->deleteJson('/api/comments/'.$comment->id.'/reactions');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Comment reaction removed successfully.')
            ->assertJsonPath('data.likes_count', 0)
            ->assertJsonPath('data.dislikes_count', 0)
            ->assertJsonPath('data.my_reaction', null);

        $this->assertDatabaseMissing('comment_reaction', [
            'comment_id' => $comment->id,
            'user_id' => $viewer->id,
        ]);
    }

    public function test_comments_by_post_can_be_sorted_by_best_rated(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $reactorOne = User::factory()->create(['char_name' => 'reactor-one']);
        $reactorTwo = User::factory()->create(['char_name' => 'reactor-two']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Sortable post',
            'content' => 'Sortable content',
            'user_id' => $postOwner->id,
        ]);

        $bestComment = Comment::query()->create([
            'description' => 'Best comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        $worstComment = Comment::query()->create([
            'description' => 'Worst comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($reactorOne);
        $this->postJson('/api/comments/'.$bestComment->id.'/reactions', ['type' => 'like']);
        $this->postJson('/api/comments/'.$worstComment->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($reactorTwo);
        $this->postJson('/api/comments/'.$bestComment->id.'/reactions', ['type' => 'like']);

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts/'.$post->id.'/comments?sort=best_rated');

        $response
            ->assertOk()
            ->assertJsonPath('context.post.id', $post->id)
            ->assertJsonPath('data.0.id', $bestComment->id)
            ->assertJsonPath('data.1.id', $worstComment->id);
    }

    public function test_comments_by_post_can_be_sorted_by_worst_rated(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $reactorOne = User::factory()->create(['char_name' => 'reactor-one']);
        $reactorTwo = User::factory()->create(['char_name' => 'reactor-two']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Sortable post',
            'content' => 'Sortable content',
            'user_id' => $postOwner->id,
        ]);

        $bestComment = Comment::query()->create([
            'description' => 'Best comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        $worstComment = Comment::query()->create([
            'description' => 'Worst comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($reactorOne);
        $this->postJson('/api/comments/'.$bestComment->id.'/reactions', ['type' => 'like']);
        $this->postJson('/api/comments/'.$worstComment->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($reactorTwo);
        $this->postJson('/api/comments/'.$worstComment->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts/'.$post->id.'/comments?sort=worst_rated');

        $response
            ->assertOk()
            ->assertJsonPath('context.post.id', $post->id)
            ->assertJsonPath('data.0.id', $worstComment->id)
            ->assertJsonPath('data.1.id', $bestComment->id);
    }

    public function test_comments_by_post_use_recent_sort_by_default(): void
    {
        $postOwner = User::factory()->create(['char_name' => 'post-owner']);
        $commentOwner = User::factory()->create(['char_name' => 'comment-owner']);
        $viewer = User::factory()->create(['char_name' => 'viewer-user']);

        $post = Post::query()->create([
            'title' => 'Sortable post',
            'content' => 'Sortable content',
            'user_id' => $postOwner->id,
        ]);

        $olderComment = Comment::query()->create([
            'description' => 'Older comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);
        $olderComment->forceFill([
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ])->save();

        $newerComment = Comment::query()->create([
            'description' => 'Newer comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);
        $newerComment->forceFill([
            'created_at' => now(),
            'updated_at' => now(),
        ])->save();

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts/'.$post->id.'/comments');

        $response
            ->assertOk()
            ->assertJsonPath('context.post.id', $post->id)
            ->assertJsonPath('data.0.id', $newerComment->id)
            ->assertJsonPath('data.1.id', $olderComment->id);
    }

    public function test_post_owner_can_delete_comment_from_another_user(): void
    {
        $postOwner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $commentOwner = User::factory()->create([
            'char_name' => 'comment-owner',
        ]);

        $post = Post::query()->create([
            'title' => 'Managed post',
            'content' => 'Managed content',
            'user_id' => $postOwner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Comment moderated by post owner',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($postOwner);

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
        $postOwner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $commentOwner = User::factory()->create([
            'char_name' => 'comment-owner',
        ]);

        $intruder = User::factory()->create([
            'char_name' => 'intruder-user',
        ]);

        $post = Post::query()->create([
            'title' => 'Protected post',
            'content' => 'Protected content',
            'user_id' => $postOwner->id,
        ]);

        $comment = Comment::query()->create([
            'description' => 'Protected comment',
            'post_id' => $post->id,
            'user_id' => $commentOwner->id,
        ]);

        Sanctum::actingAs($intruder);

        $response = $this
            ->withHeader('Accept-Language', 'pt-BR')
            ->deleteJson('/api/comments/'.$comment->id);

        $response
            ->assertStatus(403)
            ->assertJsonPath('message', 'Acesso negado.');

        $this->assertDatabaseHas('comment', [
            'id' => $comment->id,
        ]);
    }
}
