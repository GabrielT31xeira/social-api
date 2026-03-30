<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentService
{
    public function getByPost(string $post_id, string $sort = 'recent')
    {
        $post = Post::query()
            ->select(['id', 'title', 'content'])
            ->findOrFail($post_id);

        $comments = $this->decoratePaginatorWithReaction(
            $this->applySort(
                $this->baseQuery()->where('post_id', $post_id),
                $sort
            )->paginate(10),
            auth()->id()
        );

        return [
            'post' => $post,
            'comments' => $comments,
        ];
    }

    public function create(array $data): Comment
    {
        return DB::transaction(function () use ($data) {
            $comment = Comment::create([
                'description' => $data['description'],
                'post_id' => $data['post_id'],
                'user_id' => auth()->id(),
            ]);

            return $this->decorateCommentWithReaction(
                $comment->load('user:id,char_name,avatar_path')
                    ->loadCount([
                        'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                        'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
                    ]),
                auth()->id()
            );
        });
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

    public function react(string $commentId, string $userId, string $type): Comment
    {
        $comment = Comment::query()
            ->where('id', $commentId)
            ->first();

        if (!$comment) {
            throw new NotFoundHttpException(__('comment.error.not_found'));
        }

        DB::transaction(function () use ($commentId, $userId, $type) {
            CommentReaction::query()->updateOrCreate(
                [
                    'comment_id' => $commentId,
                    'user_id' => $userId,
                ],
                [
                    'type' => $type,
                ]
            );
        });

        return $this->show($commentId, $userId);
    }

    public function removeReaction(string $commentId, string $userId): Comment
    {
        $comment = Comment::query()
            ->where('id', $commentId)
            ->first();

        if (!$comment) {
            throw new NotFoundHttpException(__('comment.error.not_found'));
        }

        DB::transaction(function () use ($commentId, $userId) {
            CommentReaction::query()
                ->where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->delete();
        });

        return $this->show($commentId, $userId);
    }

    public function show(string $commentId, ?string $userId = null): Comment
    {
        $comment = $this->baseQuery()
            ->where('id', $commentId)
            ->first();

        if (!$comment) {
            throw new NotFoundHttpException(__('comment.error.not_found'));
        }

        return $this->decorateCommentWithReaction($comment, $userId ?? auth()->id());
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

    private function decorateCommentWithReaction(Comment $comment, ?string $userId): Comment
    {
        return $this->decorateCollectionWithReaction(collect([$comment]), $userId)->first();
    }

    private function decorateCollectionWithReaction(Collection $comments, ?string $userId): Collection
    {
        if (!$userId || $comments->isEmpty()) {
            return $comments->each(fn (Comment $comment) => $comment->setAttribute('my_reaction', null));
        }

        $reactions = CommentReaction::query()
            ->where('user_id', $userId)
            ->whereIn('comment_id', $comments->pluck('id'))
            ->pluck('type', 'comment_id');

        return $comments->each(function (Comment $comment) use ($reactions) {
            $comment->setAttribute('my_reaction', $reactions[$comment->id] ?? null);
        });
    }
}
