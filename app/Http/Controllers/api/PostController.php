<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\post\CreatRequest;
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

        return ApiResponse::successPaginate($posts);
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
}
