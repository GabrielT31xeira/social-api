<?php

return [
    // name
    'name_required' => 'The full name is required.',
    'name_string'   => 'The full name must be a string.',
    'name_min'      => 'The full name must be at least :min characters.',
    'name_max'      => 'The full name may not be greater than :max characters.',

    // char_name
    'char_name_required'   => 'The user name is required.',
    'char_name_string'     => 'The user name must be a string.',
    'char_name_min'        => 'The user name must be at least :min characters.',
    'char_name_max'        => 'The user name may not be greater than :max characters.',
    'char_name_alpha_dash' => 'The user name may only contain letters, numbers, dashes and underscores.',
    'char_name_unique'     => 'This user name is already taken.',
    'user_not_found'       => 'User not found.',
    // email
    'email_required' => 'The email is required.',
    'email_email'    => 'Please enter a valid email address.',
    'email_max'      => 'The email may not be greater than :max characters.',
    'email_unique'   => 'This email is already registered.',

    // password
    'password_required'  => 'The password is required.',
    'password_string'    => 'The password must be a string.',
    'password_min'       => 'The password must be at least :min characters.',
    'password_confirmed' => 'The password confirmation does not match.',

    // avatar
    'avatar_required' => 'The avatar is required.',
    'avatar_image'    => 'The avatar must be an image.',
    'avatar_mimes'    => 'The avatar must be a file of type: jpg, jpeg, png, webp.',
    'avatar_max'      => 'The avatar may not be greater than :max kilobytes.',

    // Register
    'register_success' => 'User registered successfully!',
    'register_error'   => 'Registration failed, please try again!',

    // Login
    'login_success'    => 'User logged in successfully!',
    'login_error'      => 'Login failed, please try again!',
    'login_failed'     => 'Invalid credentials.',

    // Refresh token
    'refresh_success'  => 'Token refreshed successfully!',
    'refresh_error'    => 'Failed to refresh token, please try again!',

    // Logout
    'logout_success'   => 'Logout successful!',
    'logout_error'     => 'Failed to logout, please try again!',

    // Avatar
    'avatar_updated'   => 'Avatar updated successfully!',
    'avatar_removed'   => 'Avatar removed successfully!',

    'unauthenticated' => 'Unauthenticated.',
];
