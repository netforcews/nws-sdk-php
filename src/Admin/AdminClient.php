<?php namespace Nws\Admin;

use Nws\SdkClient;

/**
 * Client used to interact with NetForce Admin
 * 
 * @property \Nws\Models\Utils\Controller $inquilinos
 * @property \Nws\Models\Utils\Controller $usuarios
 * @property \Nws\Models\Utils\Controller $tokens
 */
class AdminClient extends SdkClient
{
    use Auth;
    use Register;

    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => 'http://localhost/apps/admin/public/',
    ];

    /**
     * Inquilinos.
     * @return \Nws\Models\Utils\Controller
     */
    protected function inquilinos()
    {
        return $this->toModel('\Nws\Admin\Models\Inquilino');
    }

    /**
     * Usuarios.
     * @return \Nws\Models\Utils\Controller
     */
    protected function usuarios()
    {
        return $this->toModel('\Nws\Admin\Models\Usuario');
    }

    /**
     * Tokens.
     * @return \Nws\Models\Utils\Controller
     */
    protected function tokens()
    {
        return $this->toModel('\Nws\Admin\Models\UsuarioToken', 'usuarios/{usuario_id}/tokens/');
    }
}