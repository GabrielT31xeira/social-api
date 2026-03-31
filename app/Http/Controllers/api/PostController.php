<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\post\PostIndexRequest;
use App\Http\Requests\api\post\PostReactionRequest;
use App\Http\Requests\api\post\StorePostRequest;
use App\Http\Resources\PostListResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    public function index(PostIndexRequest $request)
    {
        $sort = $request->validated()['sort'] ?? 'recent';

        $posts = $this->postService->index($sort, $request->user()?->id);
        $posts->setCollection(
            $posts->getCollection()->map(fn ($post) => new PostListResource($post))
        );

        return ApiResponse::successPaginate($posts);
    }

    public function getByUser(PostIndexRequest $request, User $user)
    {
        $sort = $request->validated()['sort'] ?? 'recent';

        $posts = $this->postService->getByUser($user, $sort, $request->user()?->id);
        $posts->setCollection(
            $posts->getCollection()->map(fn ($post) => new PostListResource($post))
        );

        return ApiResponse::successPaginate($posts);
    }

    public function show(Request $request, Post $post)
    {
        $post = $this->postService->show($post, $request->user()?->id);

        return ApiResponse::successWithBody(
            new PostResource($post)
        );
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->store(
            $request->validated(),
            $request->user()->id
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.create')
        );
    }

    public function destroy(Request $request, Post $post)
    {
        Gate::forUser($request->user())->authorize('delete', $post);
        $this->postService->destroy($post);

        return ApiResponse::success(__('post.destroy'));
    }

    public function react(PostReactionRequest $request, Post $post)
    {
        $post = $this->postService->react(
            $post,
            $request->user()->id,
            $request->validated()['type']
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.reaction.saved')
        );
    }

    public function removeReaction(Request $request, Post $post)
    {
        $post = $this->postService->removeReaction(
            $post,
            $request->user()->id
        );

        return ApiResponse::successWithBody(
            new PostResource($post),
            __('post.reaction.removed')
        );
    }
}
