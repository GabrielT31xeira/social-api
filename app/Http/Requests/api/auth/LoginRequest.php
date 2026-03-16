<?php

namespace App\Http\Requests\api\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
                'exists:users,char_name'
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
            'char_name.exists' => 'Usuário não encontrado.'
        ];
    }
}
