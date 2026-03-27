<?php

return [

    'description' => [
        'required' => 'O comentário é obrigatório.',
        'string' => 'O comentário deve ser um texto válido.',
        'max' => 'O comentário deve ter no máximo :max caracteres.',
    ],

    'post_id' => [
        'required' => 'O post é obrigatório.',
        'uuid' => 'O identificador do post deve ser um UUID válido.',
        'exists' => 'O post informado não existe.',
    ],

    'success' => [
        'created' => 'Comentário criado com sucesso.',
        'deleted' => 'Comentário removido com sucesso.',
    ],

    'error' => [
        'not_found' => 'Comentário não encontrado.',
    ],

];
