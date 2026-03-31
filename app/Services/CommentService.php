<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\Post;
use App\Services\Concerns\InteractsWithReactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    use InteractsWithReactions;

    public function getByPost(Post $post, string $sort = 'recent', ?string $viewerId = null): LengthAwarePaginator
    {
        return $this->decoratePaginatorWithReactions(
            $this->applyReactionSort(
                $this->baseQuery()->where('post_id', $post->id),
                $sort
            )->paginate(10),
            $viewerId,
            CommentReaction::class,
            'comment_id'
        );
    }

    public function create(Post $post, array $data, string $userId): Comment
    {
        return DB::transaction(function () use ($post, $data, $userId) {
            $comment = Comment::create([
                'description' => $data['description'],
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);

            return $this->show($comment, $userId);
        });
    }

    public function destroy(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->delete();
        });
    }

    public function react(Comment $comment, string $userId, string $type): Comment
    {
        DB::transaction(function () use ($comment, $userId, $type) {
            CommentReaction::query()->updateOrCreate(
                [
                    'comment_id' => $comment->id,
                    'user_id' => $userId,
                ],
                [
                    'type' => $type,
                ]
            );
        });

        return $this->show($comment->fresh(), $userId);
    }

    public function removeReaction(Comment $comment, string $userId): Comment
    {
        DB::transaction(function () use ($comment, $userId) {
            CommentReaction::query()
                ->where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->delete();
        });

        return $this->show($comment->fresh(), $userId);
    }

    public function show(Comment $comment, ?string $userId = null): Comment
    {
        return $this->decorateModelWithReaction(
            $this->loadComment($comment),
            $userId,
            CommentReaction::class,
            'comment_id'
        );
    }

    public function summarizePost(Post $post): Post
    {
        return $post->loadMissing('user:id,char_name,avatar_path');
    }

    private function baseQuery()
    {
        return Comment::query()
            ->with(['user:id,char_name,avatar_path'])
            ->withCount([
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ]);
    }

    private function loadComment(Comment $comment): Comment
    {
        return $comment->loadMissing('user:id,char_name,avatar_path')
            ->loadCount([
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ]);
    }
}
