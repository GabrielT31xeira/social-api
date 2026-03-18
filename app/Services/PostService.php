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
            DB::transaction(function () use ($data) {
                 Post::create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'type_id' => $data['type_id'],
                    'user_id' => auth()->user()->id
                ]);
            });
            DB::commit();
            return ApiResponse::success(__("post.create"));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(__("post.error.basic"));
        }
    }

    public function destroy($post_id)
    {
        try {
            Db::transaction(function () use ($post_id) {
                Post::destroy($post_id);
            });
            DB::commit();
            return ApiResponse::success(__("post.destroy"));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(__("post.error.basic"));
        }
    }
}
