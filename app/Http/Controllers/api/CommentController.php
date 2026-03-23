<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ){}

    public function getByPost(string $postId)
    {
        $comments = $this->commentService->getByPost($postId);

        return ApiResponse::successWithBody(
            CommentResource::collection($comments),
        );
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = $this->commentService->create($request->validated());

        return ApiResponse::successWithBody(
            new CommentResource($comment->load('user:id,char_name')),
            __('comment.success.created')
        );
    }
}
