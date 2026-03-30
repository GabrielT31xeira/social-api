<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostService
{
    public function index()
    {
        return $this->baseQuery()->paginate(10);
    }

    public function getByUser(string $userId)
    {
        $userExists = User::query()
            ->where('id', $userId)
            ->exists();

        if (!$userExists) {
            throw new NotFoundHttpException(__('auth.user_not_found'));
        }

        return $this->baseQuery()
            ->where('user_id', $userId)
            ->paginate(10);
    }

    public function show(string $postId): Post
    {
        $post = $this->baseQuery()
            ->where('id', $postId)
            ->first();

        if (!$post) {
            throw new NotFoundHttpException(__('post.error.not_found'));
        }

        return $post;
    }

    public function store(array $data, string $userId): Post
    {
        return DB::transaction(function () use ($data, $userId) {
            $post = Post::create([
                'title' => $data['title'],
                'content' => json_encode($this->normalizeContentBlocks($data), JSON_UNESCAPED_UNICODE),
                'user_id' => $userId,
            ]);

            return $post->load('user:id,char_name,avatar_path')
                ->loadCount('comments');
        });
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
            ->withCount('comments')
            ->latest();
    }

    private function normalizeContentBlocks(array $data): array
    {
        $contents = $data['contents'] ?? null;

        if (is_array($contents) && $contents !== []) {
            return array_values($contents);
        }

        return [$data['content']];
    }
}
