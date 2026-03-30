<?php

return [
    "create" => "Post created!",
    "destroy" => "Post deleted!",
    "error" => [
        "basic" => "Error to create post, try again!",
        "not_found" => "Post not found.",
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
        'contents' => [
            'required' => 'At least one content block is required.',
            'array' => 'The contents field must be an array.',
            'min' => 'At least one content block is required.',
            'item_required' => 'Each content block is required.',
            'item_string' => 'Each content block must be a valid string.',
            'item_min' => 'Each content block must be at least :min characters.',
        ],
        // TYPE
        'type_id' => [
            'required' => 'The post type is required.',
            'integer' => 'The post type must be an integer.',
        ]
    ],
];
