<?php

return [
    // name
    'name_required' => 'O nome completo e obrigatorio.',
    'name_string'   => 'O nome completo deve ser uma string.',
    'name_min'      => 'O nome completo deve ter pelo menos :min caracteres.',
    'name_max'      => 'O nome completo nao pode ter mais que :max caracteres.',

    // char_name
    'char_name_required'   => 'O nome do usuario e obrigatorio.',
    'char_name_string'     => 'O nome do usuario deve ser uma string.',
    'char_name_min'        => 'O nome do usuario deve ter pelo menos :min caracteres.',
    'char_name_max'        => 'O nome do usuario nao pode ter mais que :max caracteres.',
    'char_name_alpha_dash' => 'O nome do usuario pode conter apenas letras, numeros, tracos e underscores.',
    'char_name_unique'     => 'Este nome de usuario ja esta em uso.',
    'user_not_found'       => 'Usuario nao encontrado.',

    // email
    'email_required' => 'O e-mail e obrigatorio.',
    'email_email'    => 'Informe um endereco de e-mail valido.',
    'email_max'      => 'O e-mail nao pode ter mais que :max caracteres.',
    'email_unique'   => 'Este e-mail ja esta cadastrado.',

    // password
    'password_required'  => 'A senha e obrigatoria.',
    'password_string'    => 'A senha deve ser uma string.',
    'password_min'       => 'A senha deve ter pelo menos :min caracteres.',
    'password_confirmed' => 'A confirmacao da senha nao corresponde.',

    // avatar
    'avatar_required' => 'O avatar e obrigatorio.',
    'avatar_image'    => 'O avatar deve ser uma imagem.',
    'avatar_mimes'    => 'O avatar deve ser um arquivo do tipo: jpg, jpeg, png, webp.',
    'avatar_max'      => 'O avatar nao pode ter mais que :max kilobytes.',

    // Register
    'register_success' => 'Usuario cadastrado com sucesso!',
    'register_error'   => 'Erro ao fazer o cadastro, tente novamente!',

    // Login
    'login_success'    => 'Usuario logado com sucesso!',
    'login_error'      => 'Erro ao fazer o login, tente novamente!',
    'login_failed'     => 'Credenciais invalidas.',

    // Refresh token
    'refresh_success'  => 'Token atualizado com sucesso!',
    'refresh_error'    => 'Erro ao renovar o token, tente novamente!',

    // Logout
    'logout_success'   => 'Logout realizado com sucesso!',
    'logout_error'     => 'Erro ao fazer o logout, tente novamente!',

    // Avatar
    'avatar_updated'   => 'Avatar atualizado com sucesso!',
    'avatar_removed'   => 'Avatar removido com sucesso!',

    'unauthenticated' => 'O usuario nao esta logado.',
];
