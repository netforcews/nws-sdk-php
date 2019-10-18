<?php namespace Nws\Admin;

use Nws\Credentials;

trait Auth
{
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
        $ret = $this->toJson($this->request('post', 'auth/login', [
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
        $ret = $this->toJson($this->request('get', 'auth/logout'));

        // Remover credenciais
        if ($ret['status']) {
            Credentials::set([]);
        }

        return true;
    }

    /**
     * Retorna informações do usuario logado.
     * 
     * @return \Nws\Admin\Models\Usuario
     */
    public function me()
    {
        return $this->newInstanceResult($this->request('get', 'auth/me'), '\Nws\Admin\Models\Usuario');
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