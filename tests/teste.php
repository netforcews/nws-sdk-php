<?php
use NetForce\Sdk\Auth\AuthClient;

require __DIR__ . '/../vendor/autoload.php';

$auth = new AuthClient([
    'environment' => 'sandbox',
    'credentials' => [
        'key'    => '1234',
        'secret' => '4321',
    ],
]);

$ret = $auth->login('bugotech@gmail.com', '12345678');

/**/
$me = $auth->me();

$v = $me->nome;
$v = $me->email;
$v = $me->inquilino_id;
$v = $me->created_at;
/**/

$r = $auth->logout();

$x = 1;