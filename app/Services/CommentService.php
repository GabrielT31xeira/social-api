<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    public function getByPost(string $post_id)
    {
        $post = Post::query()
            ->select(['id', 'title', 'content'])
            ->findOrFail($post_id);

        $comments = Comment::query()
            ->with(['user:id,char_name'])
            ->where('post_id', $post_id)
            ->latest()
            ->paginate(10);

        return [
            'post' => $post,
            'comments' => $comments
        ];
    }

    public function create(array $data): Comment
    {
        return Comment::create([
            'description' => $data['description'],
            'post_id' => $data['post_id'],
            'user_id' => auth()->id()
        ]);
    }
}
