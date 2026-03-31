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

    public function test_authenticated_user_can_create_post_with_multiple_content_blocks(): void
    {
        $user = User::factory()->create([
            'char_name' => 'post-creator',
        ]);

        Sanctum::actingAs($user);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->postJson('/api/posts', [
                'title' => 'Structured post',
                'contents' => [
                    'First content block',
                    'Second content block',
                    'Third content block',
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Structured post')
            ->assertJsonPath('data.content', 'First content block')
            ->assertJsonPath('data.contents.0', 'First content block')
            ->assertJsonPath('data.contents.1', 'Second content block')
            ->assertJsonPath('data.content_blocks_count', 3);
    }

    public function test_authenticated_user_can_get_paginated_posts_from_a_specific_user(): void
    {
        $targetUser = User::factory()->create([
            'char_name' => 'target-user',
        ]);

        $otherUser = User::factory()->create([
            'char_name' => 'other-user',
        ]);

        $viewer = User::factory()->create([
            'char_name' => 'viewer-user',
        ]);

        $firstPost = Post::query()->create([
            'title' => 'First target post',
            'content' => json_encode([
                'First content',
                'Hidden second block',
            ], JSON_UNESCAPED_UNICODE),
            'user_id' => $targetUser->id,
        ]);

        $secondPost = Post::query()->create([
            'title' => 'Second target post',
            'content' => json_encode([
                'Second content',
                'Another hidden block',
            ], JSON_UNESCAPED_UNICODE),
            'user_id' => $targetUser->id,
        ]);

        Post::query()->create([
            'title' => 'Other user post',
            'content' => 'Other content',
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($viewer);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->getJson('/api/users/'.$targetUser->id.'/posts');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'id' => $firstPost->id,
                'title' => 'First target post',
                'content' => 'First content',
            ])
            ->assertJsonFragment([
                'id' => $secondPost->id,
                'title' => 'Second target post',
                'content' => 'Second content',
            ]);
    }

    public function test_posts_index_can_be_sorted_by_best_rated(): void
    {
        $viewer = User::factory()->create([
            'char_name' => 'viewer-user',
        ]);

        $reactorOne = User::factory()->create([
            'char_name' => 'reactor-one',
        ]);

        $reactorTwo = User::factory()->create([
            'char_name' => 'reactor-two',
        ]);

        $postOwner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $bestPost = Post::query()->create([
            'title' => 'Best post',
            'content' => 'Best content',
            'user_id' => $postOwner->id,
        ]);

        $worstPost = Post::query()->create([
            'title' => 'Worst post',
            'content' => 'Worst content',
            'user_id' => $postOwner->id,
        ]);

        Sanctum::actingAs($reactorOne);
        $this->postJson('/api/posts/'.$bestPost->id.'/reactions', ['type' => 'like']);
        $this->postJson('/api/posts/'.$worstPost->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($reactorTwo);
        $this->postJson('/api/posts/'.$bestPost->id.'/reactions', ['type' => 'like']);

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts?sort=best_rated');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $bestPost->id)
            ->assertJsonPath('data.1.id', $worstPost->id);
    }

    public function test_posts_index_can_be_sorted_by_worst_rated(): void
    {
        $viewer = User::factory()->create([
            'char_name' => 'viewer-user',
        ]);

        $reactorOne = User::factory()->create([
            'char_name' => 'reactor-one',
        ]);

        $reactorTwo = User::factory()->create([
            'char_name' => 'reactor-two',
        ]);

        $postOwner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $bestPost = Post::query()->create([
            'title' => 'Best post',
            'content' => 'Best content',
            'user_id' => $postOwner->id,
        ]);

        $worstPost = Post::query()->create([
            'title' => 'Worst post',
            'content' => 'Worst content',
            'user_id' => $postOwner->id,
        ]);

        Sanctum::actingAs($reactorOne);
        $this->postJson('/api/posts/'.$bestPost->id.'/reactions', ['type' => 'like']);
        $this->postJson('/api/posts/'.$worstPost->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($reactorTwo);
        $this->postJson('/api/posts/'.$worstPost->id.'/reactions', ['type' => 'dislike']);

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts?sort=worst_rated');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $worstPost->id)
            ->assertJsonPath('data.1.id', $bestPost->id);
    }

    public function test_posts_index_uses_recent_sort_by_default(): void
    {
        $viewer = User::factory()->create([
            'char_name' => 'viewer-user',
        ]);

        $postOwner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $olderPost = Post::query()->create([
            'title' => 'Older post',
            'content' => 'Older content',
            'user_id' => $postOwner->id,
        ]);
        $olderPost->forceFill([
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ])->save();

        $newerPost = Post::query()->create([
            'title' => 'Newer post',
            'content' => 'Newer content',
            'user_id' => $postOwner->id,
        ]);
        $newerPost->forceFill([
            'created_at' => now(),
            'updated_at' => now(),
        ])->save();

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/posts');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $newerPost->id)
            ->assertJsonPath('data.1.id', $olderPost->id);
    }

    public function test_authenticated_user_can_get_a_specific_post_with_all_content_blocks(): void
    {
        $user = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $viewer = User::factory()->create([
            'char_name' => 'post-viewer',
        ]);

        $post = Post::query()->create([
            'title' => 'Detailed post',
            'content' => json_encode([
                'First content block',
                'Second content block',
                'Third content block',
            ], JSON_UNESCAPED_UNICODE),
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($viewer);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->getJson('/api/posts/'.$post->id);

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.content', 'First content block')
            ->assertJsonPath('data.contents.0', 'First content block')
            ->assertJsonPath('data.contents.1', 'Second content block')
            ->assertJsonPath('data.contents.2', 'Third content block')
            ->assertJsonPath('data.content_blocks_count', 3);
    }

    public function test_authenticated_user_can_like_a_post(): void
    {
        $owner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $viewer = User::factory()->create([
            'char_name' => 'post-viewer',
        ]);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($viewer);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->postJson('/api/posts/'.$post->id.'/reactions', [
                'type' => 'like',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Reaction saved successfully.')
            ->assertJsonPath('data.likes_count', 1)
            ->assertJsonPath('data.dislikes_count', 0)
            ->assertJsonPath('data.my_reaction', 'like');

        $this->assertDatabaseHas('post_reaction', [
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'type' => 'like',
        ]);
    }

    public function test_authenticated_user_can_change_reaction_from_like_to_dislike(): void
    {
        $owner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $viewer = User::factory()->create([
            'char_name' => 'post-viewer',
        ]);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($viewer);

        $this->postJson('/api/posts/'.$post->id.'/reactions', [
            'type' => 'like',
        ]);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->postJson('/api/posts/'.$post->id.'/reactions', [
                'type' => 'dislike',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.likes_count', 0)
            ->assertJsonPath('data.dislikes_count', 1)
            ->assertJsonPath('data.my_reaction', 'dislike');

        $this->assertDatabaseHas('post_reaction', [
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'type' => 'dislike',
        ]);
    }

    public function test_authenticated_user_can_remove_reaction_from_a_post(): void
    {
        $owner = User::factory()->create([
            'char_name' => 'post-owner',
        ]);

        $viewer = User::factory()->create([
            'char_name' => 'post-viewer',
        ]);

        $post = Post::query()->create([
            'title' => 'Reactable post',
            'content' => 'Reactable content',
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($viewer);

        $this->postJson('/api/posts/'.$post->id.'/reactions', [
            'type' => 'dislike',
        ]);

        $response = $this
            ->withHeader('Accept-Language', 'en')
            ->deleteJson('/api/posts/'.$post->id.'/reactions');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Reaction removed successfully.')
            ->assertJsonPath('data.likes_count', 0)
            ->assertJsonPath('data.dislikes_count', 0)
            ->assertJsonPath('data.my_reaction', null);

        $this->assertDatabaseMissing('post_reaction', [
            'post_id' => $post->id,
            'user_id' => $viewer->id,
        ]);
    }

    public function test_get_posts_by_user_returns_404_when_user_does_not_exist(): void
    {
        $viewer = User::factory()->create([
            'char_name' => 'viewer-user',
        ]);

        Sanctum::actingAs($viewer);

        $response = $this
            ->withHeader('Accept-Language', 'pt-BR')
            ->getJson('/api/users/00000000-0000-0000-0000-000000000000/posts');

        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'Usuario nao encontrado.');
    }

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
            ->deleteJson('/api/posts/'.$post->id);

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
            ->deleteJson('/api/posts/'.$post->id);

        $response
            ->assertStatus(403)
            ->assertJsonPath('message', 'Acesso negado.');

        $this->assertDatabaseHas('post', [
            'id' => $post->id,
        ]);
    }
}
