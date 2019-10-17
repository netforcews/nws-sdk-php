<?php namespace Tests\Unit\Admin;

use Tests\TestBase;
use Illuminate\Support\Arr;
use NetForce\Sdk\Models\Inquilino;
use NetForce\Sdk\Admin\AdminClient;

class AuthTest extends TestBase
{
    /**
     * @var AdminClient
     */
    protected static $admin;

    /**
     * @var string|null
     */
    protected static $inquilino_id;

    /**
     * @var array
     */
    protected static $req_inquilino = [
        'ns'       => 'testes',
        'nome'     => 'Inquilino para os testes',
        'situacao' => 'atv',
    ];

    /**
     * @var array
     */
    protected static $req_usuario = [
        'nome'     => 'Usuario de teste',
        'email'    => 'teste@netforce.com.br',
        'password' => '12345678',
        'situacao' => 'atv',
    ];

    /**
     * Preparar ambiente.
     */
    public static function setUpBeforeClass()
    {
        global $_ENV;

        static::$admin = new AdminClient([
            'environment' => AdminClient::envSandbox,

            //'credentials' => [
            //    'key'    => $_ENV['NWS_KEY'],
            //    'secret' => $_ENV['NWS_SECRET'],
            //],
        ]);
    }

    /**
     * Testar /register.
     *
     * @return void
     */
    public function testRegister()
    {
        $ret = static::$admin->register(static::$req_inquilino, static::$req_usuario);

        $this->assertInternalType('array', $ret);

        static::$inquilino_id = $ret['inquilino']->id;

        // Verificar Inquilino
        foreach (static::$req_inquilino as $key => $val) {
            $this->assertEquals(static::$req_inquilino[$key], $ret['inquilino']->{$key});
        }

        // Verificar Usuario        
        foreach (Arr::except(static::$req_usuario, ['password']) as $key => $val) {
            $this->assertEquals(static::$req_usuario[$key], $ret['usuario']->{$key});
        }
    }

    /**
     * Testar /login ERRADO
     * @depends testRegister
     * @expectedException Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Usuario ou senha incorretos
     */
    public function testLoginErrado()
    {
        static::$admin->login('x@x.com', '1234', false);
    }

    /**
     * Testar /login
     * @depends testLoginErrado
     */
    public function testLoginCerto()
    {
        $ret = static::$admin->login(static::$req_usuario['email'], static::$req_usuario['password']);

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('access_token', $ret);
    }

    /**
     * Testar /me
     * @depends testLoginCerto
     */
    public function testMe()
    {
        $me = static::$admin->me();

        //$this->assertEquals('3c8044061vc4d14184b75fb4223a6c5e43', $me->id);
        $this->assertEquals(static::$inquilino_id,            $me->inquilino_id);
        $this->assertEquals(static::$req_usuario['nome'],     $me->nome);
        $this->assertEquals(static::$req_usuario['email'],    $me->email);
        $this->assertEquals(static::$req_usuario['situacao'], $me->situacao);
    }

    /**
     * Testar /logout
     * @depends testMe
     * @expectedException Exception
     * @expectedExceptionMessage Unauthenticated.
     */
    public function testLogout()
    {
        $ret = static::$admin->logout();

        $this->assertTrue($ret);

        static::$admin->me();
    }

    /**
     * Desmontar ambiente.
     */
    public static function tearDownAfterClass()
    {
        global $_ENV;

        static::$admin->login(static::$req_usuario['email'], static::$req_usuario['password']);

        // Excluir inquilino...
        if (static::$inquilino_id) {
            $i = Inquilino::find(static::$inquilino_id, Inquilino::envSandbox);
            $i->delete();
        }
    }
}
