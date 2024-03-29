<?php namespace Tests\Unit\Admin;

use Nws\Sdk;
use Tests\TestBase;
use Tests\TestAmbiente;
use Nws\Admin\AdminClient;
use Nws\Admin\Models\UsuarioToken;

class UsuarioTokenTest extends TestBase
{
    /**
     * Preparar ambiente.
     */
    public static function setUpBeforeClass()
    {
        TestAmbiente::create();
    }

    /**
     * Carregar Admin e fazer login.
     */
    public function testAdminAndLogin()
    {
        $admin = new AdminClient([
            'environment' => Sdk::envSandbox,
        ]);

        // Fazer login
        $admin->login(TestAmbiente::$usuario['email'], TestAmbiente::$usuario['password']);

        return $admin;
    }

    /**
     * Test GET /inquilinos/id
     * 
     * @depends testAdminAndLogin
     */
    public function testCreateToken(AdminClient $admin)
    {
        $me = $admin->me();

        $token = [
            'usuario_id' => $me->id,
            'descricao' => 'Novo token',
        ];

        $tk = $admin->createUsuarioToken($token);

        $this->assertInstanceOf(UsuarioToken::class, $tk);
        $this->assertNotNull($tk->id);
        $this->assertNotNull($tk->key);
        $this->assertNotNull($tk->secret);
        $this->assertEquals($token['descricao'], $tk->descricao);
        $this->assertEquals($tk->usuario_id, $me->id);
        $this->assertEquals($tk->inquilino_id, $me->inquilino_id);

        return $admin;
    }

    /**
     * Desmontar ambiente.
     */
    public static function tearDownAfterClass()
    {
        TestAmbiente::destroy();
    }
}
