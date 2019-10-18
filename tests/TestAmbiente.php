<?php namespace Tests;

use Nws\Admin\AdminClient;

class TestAmbiente
{
    /**
     * @var array
     */
    public static $inquilino = [
        'ns'       => 'testes',
        'nome'     => 'Inquilino para os testes',
        'situacao' => 'atv',
    ];

    /**
     * @var string
     */
    public static $inquilino_id = '';

    /**
     * @var array
     */
    public static $usuario = [
        'nome'     => 'Usuario de teste',
        'email'    => 'teste@netforce.com.br',
        'password' => '12345678',
        'situacao' => 'atv',
    ];

    /**
     * Criar ambiente.
     */
    public static function create()
    {
        $admin = new AdminClient([
            'environment' => AdminClient::envSandbox,
        ]);

        $ret = $admin->register(static::$inquilino, static::$usuario);

        static::$inquilino_id = $ret['inquilino']->id;
    }

    /**
     * Desmontar ambiente.
     */
    public static function destroy()
    {
        if (!(static::$inquilino_id)) {
            return;
        }

        $admin = new AdminClient([
            'environment' => AdminClient::envSandbox,
        ]);

        // Fazer login
        $email    = static::$usuario['email'];
        $password = static::$usuario['password'];
        $admin->login($email, $password);

        // Excluir inquilino
        $admin->inquilinos->delete(['id' => static::$inquilino_id]);
    }
}