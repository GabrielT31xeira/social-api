<?php

return [
    // name
    'name_required' => 'O nome completo é obrigatório.',
    'name_string'   => 'O nome completo deve ser uma string.',
    'name_min'      => 'O nome completo deve ter pelo menos :min caracteres.',
    'name_max'      => 'O nome completo não pode ter mais que :max caracteres.',

    // char_name
    'char_name_required'   => 'O nome do personagem é obrigatório.',
    'char_name_string'     => 'O nome do personagem deve ser uma string.',
    'char_name_min'        => 'O nome do personagem deve ter pelo menos :min caracteres.',
    'char_name_max'        => 'O nome do personagem não pode ter mais que :max caracteres.',
    'char_name_alpha_dash' => 'O nome do personagem pode conter apenas letras, números, traços e underscores.',
    'char_name_unique'     => 'Este nome de personagem já está em uso.',

    // email
    'email_required' => 'O e-mail é obrigatório.',
    'email_email'    => 'Informe um endereço de e-mail válido.',
    'email_max'      => 'O e-mail não pode ter mais que :max caracteres.',
    'email_unique'   => 'Este e-mail já está cadastrado.',

    // password
    'password_required'  => 'A senha é obrigatória.',
    'password_string'    => 'A senha deve ser uma string.',
    'password_min'       => 'A senha deve ter pelo menos :min caracteres.',
    'password_confirmed' => 'A confirmação da senha não corresponde.',

    // Register
    'register_success' => 'Usuário cadastrado com sucesso!',
    'register_error'   => 'Erro ao fazer o cadastro, tente novamente!',

    // Login
    'login_success'    => 'Usuário logado com sucesso!',
    'login_error'      => 'Erro ao fazer o login, tente novamente!',
    'login_failed'     => 'Credenciais inválidas.',

    // Refresh token
    'refresh_success'  => 'Token atualizado com sucesso!',
    'refresh_error'    => 'Erro ao renovar o token, tente novamente!',

    // Logout
    'logout_success'   => 'Logout realizado com sucesso!',
    'logout_error'     => 'Erro ao fazer o logout, tente novamente!',
];
