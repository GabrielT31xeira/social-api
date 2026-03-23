<?php

namespace App\Http\Requests\api\comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // depois você pode colocar auth aqui
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
            'post_id' => ['required', 'uuid', 'exists:post,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => __('comment.description.required'),
            'description.string' => __('comment.description.string'),
            'description.max' => __('comment.description.max'),

            'post_id.required' => __('comment.post_id.required'),
            'post_id.uuid' => __('comment.post_id.uuid'),
            'post_id.exists' => __('comment.post_id.exists'),
        ];
    }
}
