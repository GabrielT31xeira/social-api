<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function index()
    {
        return Post::where('type_id', 1)
            ->with(['user:id,char_name'])
            ->latest()
            ->paginate(10);
    }

    public function store(array $data, string $userId): Post
    {
        return DB::transaction(function () use ($data, $userId) {
            return Post::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type_id' => $data['type_id'],
                'user_id' => $userId,
            ]);
        });
    }

    public function destroy(string $postId, string $userId): void
    {
        $post = Post::query()
            ->where('id', $postId)
            ->where('user_id', $userId)
            ->firstOrFail();

        DB::transaction(function () use ($post) {
            $post->delete();
        });
    }
}
