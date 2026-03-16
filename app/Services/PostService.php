<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $post = Post::create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'type_id' => $data['type_id'],
                    'user_id' => auth()->user()
                ]);
                return ApiResponse::success($post, "Post criado com sucesso!");
            });
        } catch (\Exception $e) {
            return ApiResponse::error();
        }
    }
}
