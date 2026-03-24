<?php

namespace App\Http\Requests\api\post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],

            'content' => [
                'required',
                'string',
                'min:5'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('post.validations.title.required'),
            'title.string' => __('post.validations.title.string'),
            'title.min' => __('post.validations.title.min'),
            'title.max' => __('post.validations.title.max'),

            'content.required' => __('post.validations.content.required'),
            'content.string' => __('post.validations.content.string'),
            'content.min' => __('post.validations.content.min'),
        ];
    }
}
