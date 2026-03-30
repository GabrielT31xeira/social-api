<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\post\CreatRequest;
use App\Http\Requests\api\post\PostReactionRequest;
use App\Http\Resources\PostListResource;
use App\Http\Resources\PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        $posts = $this->postService->index();
        $posts->setCollection(
            $posts->getCollection()->map(fn ($post) => new PostListResource($post))
        );

        return ApiResponse::successPaginate($posts);
    }

    public function getByUser(string $user_id)
    {
        $posts = $this->postService->getByUser($user_id);
        $posts->setCollection(
            $posts->getCollection()->map(fn ($post) => new PostListResource($post))
        );

        return ApiResponse::successPaginate($posts);
    }

    public function show(string $post_id)
    {
        $post = $this->postService->show($post_id);

        return ApiResponse::successWithBody(
            new PostResource($post)
        );
    }

    public function store(CreatRequest $request)
    {
        $post = $this->postService->store(
            $request->validated(),
            auth()->id()
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.create')
        );
    }

    public function destroy(string $post_id)
    {
        $this->postService->destroy($post_id, auth()->id());

        return ApiResponse::success(__('post.destroy'));
    }

    public function react(PostReactionRequest $request, string $post_id)
    {
        $post = $this->postService->react(
            $post_id,
            auth()->id(),
            $request->validated()['type']
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.reaction.saved')
        );
    }

    public function removeReaction(string $post_id)
    {
        $post = $this->postService->removeReaction(
            $post_id,
            auth()->id()
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.reaction.removed')
        );
    }
}
