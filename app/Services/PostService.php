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
            DB::rollBack();
            return ApiResponse::error();
        }
    }

    public function destroy($post_id)
    {
        try {
            Db::transaction(function () use ($post_id) {
                Post::destroy($post_id);
                return ApiResponse::success("", "Post removido com sucesso!");
            });
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error();
        }
    }
}
