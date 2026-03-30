<?php

return [
    "create" => "Publicação criada com sucesso!",
    "destroy" => "Post removido com sucesso!",
    "error" => [
        "basic" => "Erro por parte do servidor, tente novamente!",
        "not_found" => "Post nao encontrado.",
    ],
    'validations' => [
        // TITLE
        'title' => [
            'required' => 'O título do post é obrigatório.',
            'string' => 'O título deve ser um texto válido.',
            'min' => 'O título deve ter no mínimo :min caracteres.',
            'max' => 'O título pode ter no máximo :max caracteres.',
        ],
        // CONTENT
        'content' => [
            'required' => 'O conteúdo do post é obrigatório.',
            'string' => 'O conteúdo deve ser um texto válido.',
            'min' => 'O conteúdo deve ter no mínimo :min caracteres.',
        ],
        // TYPE
        'type_id' => [
            'required' => 'O tipo é obrigatorio.',
            'integer' => 'O tipo deve ser um inteiro.',
        ]
    ],
];
