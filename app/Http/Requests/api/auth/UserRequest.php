<?php

namespace App\Http\Requests\api\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required'    => __('auth.name_required'),
            'name.string'      => __('auth.name_string'),
            'name.min'         => __('auth.name_min'),
            'name.max'         => __('auth.name_max'),

            // char_name
            'char_name.required'   => __('auth.char_name_required'),
            'char_name.string'     => __('auth.char_name_string'),
            'char_name.min'        => __('auth.char_name_min'),
            'char_name.max'        => __('auth.char_name_max'),
            'char_name.alpha_dash' => __('auth.char_name_alpha_dash'),
            'char_name.unique'     => __('auth.char_name_unique'),

            // email
            'email.required' => __('auth.email_required'),
            'email.email'    => __('auth.email_email'),
            'email.max'      => __('auth.email_max'),
            'email.unique'   => __('auth.email_unique'),

            // password
            'password.required'      => __('auth.password_required'),
            'password.string'        => __('auth.password_string'),
            'password.min'           => __('auth.password_min'),
            'password.confirmed'     => __('auth.password_confirmed'),

            // avatar
            'avatar.image'           => __('auth.avatar_image'),
            'avatar.mimes'           => __('auth.avatar_mimes'),
            'avatar.max'             => __('auth.avatar_max'),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => $validator->errors()->first(), // primeiro erro
            'errors' => $validator->errors()            // lista completa
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
