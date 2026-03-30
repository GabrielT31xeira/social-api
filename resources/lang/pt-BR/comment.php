<?php

return [

    'description' => [
        'required' => 'O comentario e obrigatorio.',
        'string' => 'O comentario deve ser um texto valido.',
        'max' => 'O comentario deve ter no maximo :max caracteres.',
    ],

    'post_id' => [
        'required' => 'O post e obrigatorio.',
        'uuid' => 'O identificador do post deve ser um UUID valido.',
        'exists' => 'O post informado nao existe.',
    ],

    'success' => [
        'created' => 'Comentario criado com sucesso.',
        'deleted' => 'Comentario removido com sucesso.',
    ],

    'reaction' => [
        'saved' => 'Reacao do comentario salva com sucesso.',
        'removed' => 'Reacao do comentario removida com sucesso.',
        'validation' => [
            'required' => 'O tipo de reacao e obrigatorio.',
            'in' => 'O tipo de reacao deve ser like ou dislike.',
        ],
    ],

    'error' => [
        'not_found' => 'Comentario nao encontrado.',
    ],

];
