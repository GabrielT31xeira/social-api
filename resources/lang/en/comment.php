<?php

return [

    'description' => [
        'required' => 'The comment is required.',
        'string' => 'The comment must be a valid text.',
        'max' => 'The comment may not be greater than :max characters.',
    ],

    'post_id' => [
        'required' => 'The post is required.',
        'uuid' => 'The post ID must be a valid UUID.',
        'exists' => 'The selected post does not exist.',
    ],

    'success' => [
        'created' => 'Comment created successfully.',
    ],

    'error' => [
        'not_found' => 'Comment not found.',
    ],

];
