<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'content' => $this->first_content_block,
            'content_blocks_count' => $this->content_blocks_count,
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
