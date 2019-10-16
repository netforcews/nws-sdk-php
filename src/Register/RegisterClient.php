<?php namespace NetForce\Sdk\Register;

use NetForce\Sdk\SdkClient;

class RegisterClient extends SdkClient
{
    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => 'http://localhost/apps/admin/public/',
    ];

    /**
     * Registrar um novo inquilino e usuario admin.
     * 
     * @param array $inquilino
     * @param array $usuario
     * @return array
     */
    public function register($inquilino, $usuario)
    {
        $ret = $this->toJson($this->request('post', 'register', [
            'json' => [
                'inquilino' => $inquilino,
                'usuario' => $usuario,
            ],
        ]));

        return [
            'inquilino' => $this->toResponse($ret['inquilino']),
            'usuario'   => $this->toResponse($ret['usuario']),
        ];
    }
}