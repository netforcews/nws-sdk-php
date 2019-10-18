<?php
/**
 * Funções do servico Admin
 * Versão: 2019-10-18
 */

return [
    //
    // Inquilinos
    //
    'listInquilinos' => [
        'action' => 'list',
        'uri'    => 'inquilinos/',
        'result' => '\Nws\Admin\Models\Inquilino',
    ],

    'getInquilino' => [
        'action' => 'get',
        'uri'    => 'inquilinos/{id}',
        'result' => '\Nws\Admin\Models\Inquilino',
    ],

    'createInquilino' => [
        'action' => 'create',
        'uri'    => 'inquilinos/',
        'result' => '\Nws\Admin\Models\Inquilino',
    ],

    'updateInquilino' => [
        'action' => 'update',
        'uri'    => 'inquilinos/{id}',
        'result' => '\Nws\Admin\Models\Inquilino',
    ],

    'deleteInquilino' => [
        'action' => 'delete',
        'uri'    => 'inquilinos/{id}',
    ],

    //
    // Usuarios
    //
    'listUsuarios' => [
        'action' => 'list',
        'uri'    => 'usuarios/',
        'result' => '\Nws\Admin\Models\Usuario',
    ],

    'getUsuario' => [
        'action' => 'get',
        'uri'    => 'usuarios/{id}',
        'result' => '\Nws\Admin\Models\Usuario',
    ],

    'createUsuario' => [
        'action' => 'create',
        'uri'    => 'usuarios/',
        'result' => '\Nws\Admin\Models\Usuario',
    ],

    'updateUsuario' => [
        'action' => 'update',
        'uri'    => 'usuarios/{id}',
        'result' => '\Nws\Admin\Models\Usuario',
    ],

    'deleteUsuario' => [
        'action' => 'delete',
        'uri'    => 'usuarios/{id}',
    ],

    //
    // Usuario Tokens
    //
    'listUsuarioTokens' => [
        'action' => 'list',
        'uri'    => 'usuarios/{usuario_id}/tokens/',
        'result' => '\Nws\Admin\Models\UsuarioToken',
    ],

    'getUsuarioToken' => [
        'action' => 'get',
        'uri'    => 'usuarios/{usuario_id}/tokens/{id}',
        'result' => '\Nws\Admin\Models\UsuarioToken',
    ],

    'createUsuarioToken' => [
        'action' => 'create',
        'uri'    => 'usuarios/{usuario_id}/tokens/',
        'result' => '\Nws\Admin\Models\UsuarioToken',
    ],

    'updateUsuarioToken' => [
        'action' => 'update',
        'uri'    => 'usuarios/{usuario_id}/tokens/{id}',
        'result' => '\Nws\Admin\Models\UsuarioToken',
    ],

    'deleteUsuarioToken' => [
        'action' => 'delete',
        'uri'    => 'usuarios/{usuario_id}/tokens/{id}',
    ],
];