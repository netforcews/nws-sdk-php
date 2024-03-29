<?php namespace Tests\Unit\Admin;

use Nws\Sdk;
use Tests\TestBase;
use Tests\TestAmbiente;
use Nws\Collection;
use Nws\Admin\AdminClient;
use Nws\Admin\Models\Inquilino;

class InquilinoTest extends TestBase
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
    public function testGetId(AdminClient $admin)
    {
        $inq = $admin->getInquilino(['id' => TestAmbiente::$inquilino_id]);

        $this->assertInstanceOf(Inquilino::class, $inq);
        $this->assertEquals(TestAmbiente::$inquilino_id, $inq->id);

        return $admin;
    }

    /**
     * Test GET /inquilinos
     * 
     * @depends testGetId
     */
    public function testGetList(AdminClient $admin)
    {
        $col = $admin->listInquilinos();

        $this->assertInstanceOf(Collection::class, $col);
        $this->assertGreaterThanOrEqual(1, $col->count());

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
