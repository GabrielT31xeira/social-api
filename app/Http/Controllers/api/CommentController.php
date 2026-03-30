<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\comment\CommentReactionRequest;
use App\Http\Requests\api\comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ){}

    public function getByPost(Request $request, string $post_id)
    {
        $sort = $request->validate([
            'sort' => ['nullable', 'in:recent,best_rated,worst_rated'],
        ])['sort'] ?? 'recent';

        $payload = $this->commentService->getByPost($post_id, $sort);
        $payload['comments']->setCollection(
            $payload['comments']->getCollection()->map(fn ($comment) => new CommentResource($comment))
        );

        return ApiResponse::successWithBody(
            $payload
        );
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = $this->commentService->create($request->validated());

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.success.created')
        );
    }

    public function destroy(string $comment_id)
    {
        $this->commentService->destroy($comment_id, auth()->id());

        return ApiResponse::success(
            __('comment.success.deleted')
        );
    }

    public function react(CommentReactionRequest $request, string $comment_id)
    {
        $comment = $this->commentService->react(
            $comment_id,
            auth()->id(),
            $request->validated()['type']
        );

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.reaction.saved')
        );
    }

    public function removeReaction(string $comment_id)
    {
        $comment = $this->commentService->removeReaction(
            $comment_id,
            auth()->id()
        );

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.reaction.removed')
        );
    }
}
