<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostService
{
    public function index(string $sort = 'recent')
    {
        return $this->decoratePaginatorWithReaction(
            $this->applySort($this->baseQuery(), $sort)->paginate(10),
            auth()->id()
        );
    }

    public function getByUser(string $userId, string $sort = 'recent')
    {
        $userExists = User::query()
            ->where('id', $userId)
            ->exists();

        if (!$userExists) {
            throw new NotFoundHttpException(__('auth.user_not_found'));
        }

        return $this->decoratePaginatorWithReaction(
            $this->applySort(
                $this->baseQuery()->where('user_id', $userId),
                $sort
            )->paginate(10),
            auth()->id()
        );
    }

    public function show(string $postId): Post
    {
        $post = $this->baseQuery()
            ->where('id', $postId)
            ->first();

        if (!$post) {
            throw new NotFoundHttpException(__('post.error.not_found'));
        }

        return $this->decoratePostWithReaction($post, auth()->id());
    }

    public function store(array $data, string $userId): Post
    {
        return DB::transaction(function () use ($data, $userId) {
            $post = Post::create([
                'title' => $data['title'],
                'content' => json_encode($this->normalizeContentBlocks($data), JSON_UNESCAPED_UNICODE),
                'user_id' => $userId,
            ]);

            return $this->decoratePostWithReaction(
                $post->load('user:id,char_name,avatar_path')
                    ->loadCount([
                        'comments',
                        'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                        'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
                    ]),
                $userId
            );
        });
    }

    public function react(string $postId, string $userId, string $type): Post
    {
        $post = Post::query()
            ->where('id', $postId)
            ->first();

        if (!$post) {
            throw new NotFoundHttpException(__('post.error.not_found'));
        }

        DB::transaction(function () use ($postId, $userId, $type) {
            PostReaction::query()->updateOrCreate(
                [
                    'post_id' => $postId,
                    'user_id' => $userId,
                ],
                [
                    'type' => $type,
                ]
            );
        });

        return $this->show($postId);
    }

    public function removeReaction(string $postId, string $userId): Post
    {
        $post = Post::query()
            ->where('id', $postId)
            ->first();

        if (!$post) {
            throw new NotFoundHttpException(__('post.error.not_found'));
        }

        DB::transaction(function () use ($postId, $userId) {
            PostReaction::query()
                ->where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();
        });

        return $this->show($postId);
    }

    public function destroy(string $postId, string $userId): void
    {
        $post = Post::query()
            ->where('id', $postId)
            ->first();

        if (!$post || $post->user_id !== $userId) {
            throw new NotFoundHttpException(__('post.error.not_found'));
        }

        DB::transaction(function () use ($post) {
            $post->delete();
        });
    }

    private function baseQuery()
    {
        return Post::with(['user:id,char_name,avatar_path'])
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

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'best_rated' => $query
                ->orderByRaw('(likes_count - dislikes_count) DESC')
                ->orderByDesc('likes_count')
                ->latest(),
            'worst_rated' => $query
                ->orderByRaw('(dislikes_count - likes_count) DESC')
                ->orderByDesc('dislikes_count')
                ->latest(),
            default => $query->latest(),
        };
    }

    private function decoratePaginatorWithReaction($paginator, ?string $userId)
    {
        $paginator->setCollection(
            $this->decorateCollectionWithReaction($paginator->getCollection(), $userId)
        );

        return $paginator;
    }

    private function decoratePostWithReaction(Post $post, ?string $userId): Post
    {
        return $this->decorateCollectionWithReaction(collect([$post]), $userId)->first();
    }

    private function decorateCollectionWithReaction(Collection $posts, ?string $userId): Collection
    {
        if (!$userId || $posts->isEmpty()) {
            return $posts->each(fn (Post $post) => $post->setAttribute('my_reaction', null));
        }

        $reactions = PostReaction::query()
            ->where('user_id', $userId)
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('type', 'post_id');

        return $posts->each(function (Post $post) use ($reactions) {
            $post->setAttribute('my_reaction', $reactions[$post->id] ?? null);
        });
    }
}
