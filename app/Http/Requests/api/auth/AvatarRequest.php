<?php

namespace App\Http\Requests\api\auth;

use App\Http\Requests\ApiFormRequest;

class AvatarRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => __('auth.avatar_required'),
            'avatar.image' => __('auth.avatar_image'),
            'avatar.mimes' => __('auth.avatar_mimes'),
            'avatar.max' => __('auth.avatar_max'),
        ];
    }
}
