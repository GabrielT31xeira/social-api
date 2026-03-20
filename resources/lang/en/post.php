<?php

return [
    "create" => "Post created!",
    "destroy" => "Post deleted!",
    "error" => [
        "basic" => "Error to create post, try again!",
    ],
    'validations' => [
        // TITLE
        'title' => [
            'required' => 'The post title is required.',
            'string' => 'The title must be a valid string.',
            'min' => 'The title must be at least :min characters.',
            'max' => 'The title may not be greater than :max characters.',
        ],
        // CONTENT
        'content' => [
            'required' => 'The post content is required.',
            'string' => 'The content must be a valid string.',
            'min' => 'The content must be at least :min characters.',
        ],
        // TYPE
        'type_id' => [
            'required' => 'The post type is required.',
            'integer' => 'The post type must be an integer.',
        ]
    ],
];
