<?php

namespace App\Http\Requests\api\comment;

use App\Http\Requests\ApiFormRequest;

class CommentIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => ['nullable', 'in:recent,best_rated,worst_rated'],
        ];
    }
}
