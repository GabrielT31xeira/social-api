<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\post\CreatRequest;
use App\Services\PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function store(CreatRequest $request)
    {
        return ApiResponse::success($this->postService->store($request));
    }

    public function destroy($post_id)
    {
        return ApiResponse::success();
    }
}
