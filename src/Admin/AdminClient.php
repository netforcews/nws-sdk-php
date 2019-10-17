<?php namespace NetForce\Sdk\Admin;

use NetForce\Sdk\SdkClient;

/**
 * Client used to interact with NetForce Admin
 * 
 * @property \NetForce\Sdk\Models\Utils\Controller $inquilinos
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
     * @return \NetForce\Sdk\Models\Utils\Controller
     */
    protected function inquilinos()
    {
        return $this->toModel('\NetForce\Sdk\Admin\Models\Inquilino');
    }

    /**
     * Usuarios.
     * @return \NetForce\Sdk\Models\Utils\Controller
     */
    protected function usuarios()
    {
        return $this->toModel('\NetForce\Sdk\Admin\Models\Usuario');
    }

    /**
     * Tokens.
     * @return \NetForce\Sdk\Models\Utils\Controller
     */
    protected function tokens()
    {
        return $this->toModel('\NetForce\Sdk\Admin\Models\UsuarioToken', 'usuarios/{usuario_id}/tokens/');
    }
}