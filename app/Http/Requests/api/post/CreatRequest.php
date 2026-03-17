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
            // TITLE
            'title.required' => 'O título do post é obrigatório.',
            'title.string' => 'O título deve ser um texto válido.',
            'title.min' => 'O título deve ter no mínimo :min caracteres.',
            'title.max' => 'O título pode ter no máximo :max caracteres.',

            // CONTENT
            'content.required' => 'O conteúdo do post é obrigatório.',
            'content.string' => 'O conteúdo deve ser um texto válido.',
            'content.min' => 'O conteúdo deve ter no mínimo :min caracteres.',
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
