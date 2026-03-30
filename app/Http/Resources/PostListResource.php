<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'content' => $this->first_content_block,
            'content_blocks_count' => $this->content_blocks_count,
            'comments_count' => $this->comments_count,
            'likes_count' => $this->likes_count ?? 0,
            'dislikes_count' => $this->dislikes_count ?? 0,
            'my_reaction' => $this->my_reaction,
            'user' => [
                'id' => $this->user->id ?? null,
                'char_name' => $this->user->char_name ?? null,
                'avatar_url' => $this->user->avatar_url ?? null,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
