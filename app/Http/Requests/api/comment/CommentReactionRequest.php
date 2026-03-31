<?php

namespace App\Http\Requests\api\comment;

use App\Http\Requests\ApiFormRequest;

class CommentReactionRequest extends ApiFormRequest
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
            'type.required' => __('comment.reaction.validation.required'),
            'type.in' => __('comment.reaction.validation.in'),
        ];
    }
}
