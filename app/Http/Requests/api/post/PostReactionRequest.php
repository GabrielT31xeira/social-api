<?php

namespace App\Http\Requests\api\post;

use App\Http\Requests\ApiFormRequest;

class PostReactionRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'in:like,dislike',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => __('post.reaction.validation.required'),
            'type.in' => __('post.reaction.validation.in'),
        ];
    }
}
