<?php

namespace App\Http\Requests\api\auth;

use App\Http\Requests\ApiFormRequest;

class LoginRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'char_name' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'char_name.required' => __('auth.char_name_required'),
            'char_name.string'   => __('auth.char_name_string'),
            'password.required'  => __('auth.password_required'),
            'password.string'    => __('auth.password_string'),
        ];
    }
}
