<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    public function getByPost(string $postId): LengthAwarePaginator
    {
        return Comment::query()
            ->with(['user:id,char_name'])
            ->where('post_id', $postId)
            ->latest()
            ->paginate(10);
    }

    public function create(array $data): Comment
    {
        return Comment::create([
            'description' => $data['description'],
            'post_id' => $data['post_id'],
            'user_id' => auth()->user()->id
        ]);
    }
}
