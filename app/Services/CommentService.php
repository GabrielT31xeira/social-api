<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentService
{
    public function getByPost(string $post_id)
    {
        $post = Post::query()
            ->select(['id', 'title', 'content'])
            ->findOrFail($post_id);

        $comments = Comment::query()
            ->with(['user:id,char_name,avatar_path'])
            ->where('post_id', $post_id)
            ->latest()
            ->paginate(10);

        return [
            'post' => $post,
            'comments' => $comments,
        ];
    }

    public function create(array $data): Comment
    {
        return DB::transaction(function () use ($data) {
            return Comment::create([
                'description' => $data['description'],
                'post_id' => $data['post_id'],
                'user_id' => auth()->id(),
            ]);
        })->load('user:id,char_name,avatar_path');
    }

    public function destroy(string $commentId, string $userId): void
    {
        $comment = Comment::query()
            ->with('post:id,user_id')
            ->where('id', $commentId)
            ->first();

        if (
            !$comment ||
            ($comment->user_id !== $userId && $comment->post?->user_id !== $userId)
        ) {
            throw new NotFoundHttpException(__('comment.error.not_found'));
        }

        DB::transaction(function () use ($comment) {
            $comment->delete();
        });
    }
}
