<?php namespace Tests\Unit\Admin;

use Nws\Sdk;
use Tests\TestBase;
use Tests\TestAmbiente;
use Illuminate\Support\Arr;
use Nws\Admin\AdminClient;

class AuthTest extends TestBase
{
    /**
     * Carregar Admin.
     */
    public function testAdmin()
    {
        $admin = new AdminClient([
            'environment' => Sdk::envSandbox,
        ]);

        return $admin;
    }

     /**
     * Testar /register.
     *
     * @return void
     * @depends testAdmin
     */
    public function testRegister(AdminClient $admin)
    {
        $ret = $admin->register(TestAmbiente::$inquilino, TestAmbiente::$usuario);

        $this->assertInternalType('array', $ret);

        TestAmbiente::$inquilino_id = $ret['inquilino']->id;

        // Verificar Inquilino
        foreach (TestAmbiente::$inquilino as $key => $val) {
            $this->assertEquals(TestAmbiente::$inquilino[$key], $ret['inquilino']->{$key});
        }

        // Verificar Usuario        
        foreach (Arr::except(TestAmbiente::$usuario, ['password']) as $key => $val) {
            $this->assertEquals(TestAmbiente::$usuario[$key], $ret['usuario']->{$key});
        }

        return $admin;
    }

    /**
     * Testar /login ERRADO
     * @depends testRegister
     * xpectedException Exception
     * xpectedExceptionCode 400
     * xpectedExceptionMessage Usuario ou senha incorretos
     */
    public function testLoginErrado(AdminClient $admin)
    {
        try {
            $admin->login('x@x.com', '1234', false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertEquals('400', $e->getCode());
            $this->assertEquals('Usuario ou senha incorretos', $e->getMessage());
        }

        return $admin;
    }

    /**
     * Testar /login
     * @depends testLoginErrado
     */
    public function testLoginCerto(AdminClient $admin)
    {
        $email    = TestAmbiente::$usuario['email'];
        $password = TestAmbiente::$usuario['password'];

        $ret = $admin->login($email, $password);

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('access_token', $ret);

        return ['admin' => $admin, 'access_token' => $ret['access_token']];
    }

    /**
     * Testar /me
     * @depends testLoginCerto
     */
    public function testMe($info)
    {
        $admin = $info['admin'];

        $me = $admin->me();

        $user = TestAmbiente::$usuario;

        //$this->assertEquals('3c8044061vc4d14184b75fb4223a6c5e43', $me->id);
        $this->assertEquals(TestAmbiente::$inquilino_id, $me->inquilino_id);
        $this->assertEquals($user['nome'],               $me->nome);
        $this->assertEquals($user['email'],              $me->email);
        $this->assertEquals($user['situacao'],           $me->situacao);

        return $admin;
    }

    /**
     * Testar /me - via accesstoken
     * @depends testLoginCerto
     */
    public function testMeViaAccessToken($info)
    {
        // Criar novo Admin não logado ainda
        $admin = new AdminClient([
            'environment' => Sdk::envSandbox,
            'credentials' => [
                'access_token' => $info['access_token'],
            ],
        ]);

        $me = $admin->me();

        $user = TestAmbiente::$usuario;

        //$this->assertEquals('3c8044061vc4d14184b75fb4223a6c5e43', $me->id);
        $this->assertEquals(TestAmbiente::$inquilino_id, $me->inquilino_id);
        $this->assertEquals($user['nome'],               $me->nome);
        $this->assertEquals($user['email'],              $me->email);
        $this->assertEquals($user['situacao'],           $me->situacao);

        return $info;
    }

    /**
     * Testar /logout
     * @depends testMe
     * @expectedException Exception
     * @expectedExceptionMessage Unauthenticated.
     */
    public function testLogout(AdminClient $admin)
    {
        $ret = $admin->logout();

        $this->assertTrue($ret);

        $admin->me();
    }

    /**
     * Desmontar ambiente.
     */
    public static function tearDownAfterClass()
    {
        TestAmbiente::destroy();
    }
}
