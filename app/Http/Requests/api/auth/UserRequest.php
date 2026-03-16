<?php

namespace App\Http\Requests\api\auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],

            'char_name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                'unique:users,char_name'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'char_name.unique' => 'Este nome de personagem já está em uso.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.confirmed' => 'As senhas não coincidem.'
        ];
    }
}
