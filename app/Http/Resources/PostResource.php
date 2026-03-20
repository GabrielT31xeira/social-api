<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,

            'title' => $this->title,
            'content' => $this->content,

            // Relacionamentos (só carrega se vier com ->with())
            'user' => new UserResource($this->whenLoaded('user')),

            'type' => [
                'id' => $this->whenLoaded('type', fn () => $this->type->id),
                'name' => $this->whenLoaded('type', fn () => $this->type->post),
            ],

            // Datas formatadas (padrão API)
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
