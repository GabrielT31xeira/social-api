<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\comment\CommentIndexRequest;
use App\Http\Requests\api\comment\CommentReactionRequest;
use App\Http\Requests\api\comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ){}

    public function getByPost(CommentIndexRequest $request, Post $post)
    {
        $sort = $request->validated()['sort'] ?? 'recent';

        $comments = $this->commentService->getByPost(
            $post,
            $sort,
            $request->user()?->id
        );
        $comments->setCollection(
            $comments->getCollection()->map(fn ($comment) => new CommentResource($comment))
        );

        return ApiResponse::successPaginate(
            $comments,
            ['post' => new PostSummaryResource($this->commentService->summarizePost($post))]
        );
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = $this->commentService->create(
            $post,
            $request->validated(),
            $request->user()->id
        );

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.success.created')
        );
    }

    public function destroy(Request $request, Comment $comment)
    {
        Gate::forUser($request->user())->authorize('delete', $comment);
        $this->commentService->destroy($comment);

        return ApiResponse::success(
            __('comment.success.deleted')
        );
    }

    public function react(CommentReactionRequest $request, Comment $comment)
    {
        $comment = $this->commentService->react(
            $comment,
            $request->user()->id,
            $request->validated()['type']
        );

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.reaction.saved')
        );
    }

    public function removeReaction(Request $request, Comment $comment)
    {
        $comment = $this->commentService->removeReaction(
            $comment,
            $request->user()->id
        );

        return ApiResponse::successWithBody(
            new CommentResource($comment),
            __('comment.reaction.removed')
        );
    }
}
