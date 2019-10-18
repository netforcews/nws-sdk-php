<?php
use Nws\Admin\AdminClient;

require __DIR__ . '/../vendor/autoload.php';

$admin = new AdminClient([
    'environment' => AdminClient::envSandbox,
]);

/**/
$admin->setCredentials([
    'key'    => '1234',
    'secret' => '4321',
]);/**/

$x = $admin->inquilinos->get('2ac5f0351vdf4e49f7b6923ee2fc235c4c');

$x = 1;