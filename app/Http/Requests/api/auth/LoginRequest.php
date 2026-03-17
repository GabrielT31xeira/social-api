<?php

namespace App\Http\Requests\api\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
            'char_name.required' => __('auth.char_name_required'),
            'char_name.string'   => __('auth.char_name_string'),
            'char_name.exists'   => __('auth.user_not_found'),
            'password.required'  => __('auth.password_required'),
            'password.string'    => __('auth.password_string'),
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
