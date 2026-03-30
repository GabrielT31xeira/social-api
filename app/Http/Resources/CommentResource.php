<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'likes_count' => $this->likes_count ?? 0,
            'dislikes_count' => $this->dislikes_count ?? 0,
            'my_reaction' => $this->my_reaction,
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id ?? null,
                'char_name' => $this->user->char_name ?? null,
                'avatar_url' => $this->user->avatar_url ?? null,
            ],
        ];
    }
}
