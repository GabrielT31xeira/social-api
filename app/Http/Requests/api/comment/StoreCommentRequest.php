<?php

namespace App\Http\Requests\api\comment;

use App\Http\Requests\ApiFormRequest;

class StoreCommentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => __('comment.description.required'),
            'description.string' => __('comment.description.string'),
            'description.max' => __('comment.description.max'),
        ];
    }
}
