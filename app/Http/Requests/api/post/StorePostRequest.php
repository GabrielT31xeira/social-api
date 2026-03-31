<?php

namespace App\Http\Requests\api\post;

use App\Http\Requests\ApiFormRequest;

class StorePostRequest extends ApiFormRequest
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
                'max:255',
            ],
            'content' => [
                'required_without:contents',
                'nullable',
                'string',
                'min:5',
            ],
            'contents' => [
                'required_without:content',
                'nullable',
                'array',
                'min:1',
            ],
            'contents.*' => [
                'required',
                'string',
                'min:5',
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
            'content.required_without' => __('post.validations.content.required'),
            'content.string' => __('post.validations.content.string'),
            'content.min' => __('post.validations.content.min'),
            'contents.required_without' => __('post.validations.contents.required'),
            'contents.array' => __('post.validations.contents.array'),
            'contents.min' => __('post.validations.contents.min'),
            'contents.*.required' => __('post.validations.contents.item_required'),
            'contents.*.string' => __('post.validations.contents.item_string'),
            'contents.*.min' => __('post.validations.contents.item_min'),
        ];
    }
}
