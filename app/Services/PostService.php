<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use App\Services\Concerns\InteractsWithReactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    use InteractsWithReactions;

    public function index(string $sort = 'recent', ?string $viewerId = null): LengthAwarePaginator
    {
        return $this->decoratePaginatorWithReactions(
            $this->applyReactionSort($this->baseQuery(), $sort)->paginate(10),
            $viewerId,
            PostReaction::class,
            'post_id'
        );
    }

    public function getByUser(User $user, string $sort = 'recent', ?string $viewerId = null): LengthAwarePaginator
    {
        return $this->decoratePaginatorWithReactions(
            $this->applyReactionSort(
                $this->baseQuery()->where('user_id', $user->id),
                $sort
            )->paginate(10),
            $viewerId,
            PostReaction::class,
            'post_id'
        );
    }

    public function show(Post $post, ?string $viewerId = null): Post
    {
        return $this->decorateModelWithReaction(
            $this->loadPost($post),
            $viewerId,
            PostReaction::class,
            'post_id'
        );
    }

    public function store(array $data, string $userId): Post
    {
        return DB::transaction(function () use ($data, $userId) {
            $post = Post::create([
                'title' => $data['title'],
                'content' => json_encode($this->normalizeContentBlocks($data), JSON_UNESCAPED_UNICODE),
                'user_id' => $userId,
            ]);

            return $this->show($post, $userId);
        });
    }

    public function react(Post $post, string $userId, string $type): Post
    {
        DB::transaction(function () use ($post, $userId, $type) {
            PostReaction::query()->updateOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $userId,
                ],
                [
                    'type' => $type,
                ]
            );
        });

        return $this->show($post->fresh(), $userId);
    }

    public function removeReaction(Post $post, string $userId): Post
    {
        DB::transaction(function () use ($post, $userId) {
            PostReaction::query()
                ->where('post_id', $post->id)
                ->where('user_id', $userId)
                ->delete();
        });

        return $this->show($post->fresh(), $userId);
    }

    public function destroy(Post $post): void
    {
        DB::transaction(function () use ($post) {
            $post->delete();
        });
    }

    private function baseQuery()
    {
        return Post::query()
            ->with(['user:id,char_name,avatar_path'])
            ->withCount([
                'comments',
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ]);
    }

    private function normalizeContentBlocks(array $data): array
    {
        $contents = $data['contents'] ?? null;

        if (is_array($contents) && $contents !== []) {
            return array_values($contents);
        }

        return [$data['content']];
    }

    private function loadPost(Post $post): Post
    {
        return $post->loadMissing('user:id,char_name,avatar_path')
            ->loadCount([
                'comments',
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ]);
    }
}
