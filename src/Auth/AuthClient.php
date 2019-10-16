<?php namespace NetForce\Sdk\Auth;

use NetForce\Sdk\SdkClient;
use NetForce\Sdk\Credentials;

class AuthClient extends SdkClient
{
    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => 'http://localhost/apps/admin/public/auth/',
    ];

    /**
     * Executar login.
     * 
     * @param string $email
     * @param string $password
     * @param bool $guard Se maracdo com true, ira guardar o access token para futuras requisições.
     * @return mixed
     */
    public function login($email, $password, $guard = true)
    {
        $ret = $this->toJson($this->request('post', 'login', [
            'json' => [
                'email'    => $email,
                'password' => $password,
            ],
        ]));

        if ($guard) {
            Credentials::set($ret);
        }

        return $ret;
    }

    /**
     * Se tiver logado, executar o logout.
     */
    public function logout()
    {
        $ret = $this->toJson($this->request('get', 'logout'));

        // Remover credenciais
        if ($ret['status']) {
            Credentials::set([]);
        }

        return true;
    }

    /**
     * Retorna informações do usuario logado.
     * 
     * @return \NetForce\Sdk\Response
     */
    public function me()
    {
        return $this->toResponse($this->request('get', 'me'));
    }

    /**
     * Atribuir novas credenciais.
     * 
     * @param array $credentials
     */
    public function setCredentials($credentials)
    {
        Credentials::set($credentials);
    }
}