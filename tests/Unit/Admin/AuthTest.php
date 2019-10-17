<?php namespace Tests\Unit\Admin;

use Tests\TestBase;
use Illuminate\Support\Arr;
use NetForce\Sdk\Models\Inquilino;
use NetForce\Sdk\Admin\AdminClient;
use Tests\TestAmbiente;

class AuthTest extends TestBase
{
    /**
     * Carregar Admin.
     */
    public function testAdmin()
    {
        $admin = new AdminClient([
            'environment' => AdminClient::envSandbox,
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

        return $admin;
    }

    /**
     * Testar /me
     * @depends testLoginCerto
     */
    public function testMe(AdminClient $admin)
    {
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
