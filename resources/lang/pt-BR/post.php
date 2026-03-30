<?php

return [
    'create' => 'Publicacao criada com sucesso!',
    'destroy' => 'Post removido com sucesso!',
    'error' => [
        'basic' => 'Erro por parte do servidor, tente novamente!',
        'not_found' => 'Post nao encontrado.',
    ],
    'reaction' => [
        'saved' => 'Reacao salva com sucesso.',
        'removed' => 'Reacao removida com sucesso.',
        'validation' => [
            'required' => 'O tipo de reacao e obrigatorio.',
            'in' => 'O tipo de reacao deve ser like ou dislike.',
        ],
    ],
    'validations' => [
        'title' => [
            'required' => 'O titulo do post e obrigatorio.',
            'string' => 'O titulo deve ser um texto valido.',
            'min' => 'O titulo deve ter no minimo :min caracteres.',
            'max' => 'O titulo pode ter no maximo :max caracteres.',
        ],
        'content' => [
            'required' => 'O conteudo do post e obrigatorio.',
            'string' => 'O conteudo deve ser um texto valido.',
            'min' => 'O conteudo deve ter no minimo :min caracteres.',
        ],
        'contents' => [
            'required' => 'Informe pelo menos um bloco de conteudo.',
            'array' => 'O campo de blocos de conteudo deve ser um array.',
            'min' => 'Informe pelo menos um bloco de conteudo.',
            'item_required' => 'Cada bloco de conteudo e obrigatorio.',
            'item_string' => 'Cada bloco de conteudo deve ser um texto valido.',
            'item_min' => 'Cada bloco de conteudo deve ter no minimo :min caracteres.',
        ],
        'type_id' => [
            'required' => 'O tipo e obrigatorio.',
            'integer' => 'O tipo deve ser um inteiro.',
        ],
    ],
];
