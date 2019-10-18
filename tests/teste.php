<?php
use Nws\Admin\AdminClient;
use Nws\Sdk;

require __DIR__ . '/../vendor/autoload.php';

$admin = new AdminClient([
    'environment' => Sdk::envSandbox,
]);

$admin->setCredentials([
    'key'    => 'c1QxMcjv6vTetgHvxkcesVyidnIiyRZZC2Uxh4J4ukR1NPckUeZd7nVNPEDy',
    'secret' => 'AgIwLR6B1l3gHkM0NjmWdGHKD1yYymW4Wu0QKDkXPekMVbwA3wUxuO2z6I8Q',
]);

$inq = $admin->createInquilino([
    'ns'       => 'meuteste',
    'nome'     => 'Meu Teste',
    'situacao' => 'atv',
]);

$lista = $admin->listInquilinos();

$x = $admin->getInquilino(['id' => $inq->id]);

$x = $admin->updateInquilino([
    'id' => $inq->id,
    'nome' => 'Nome 2',
]);

$admin->deleteInquilino(['id' => $inq->id]);

//$x = $admin->inquilinos->get('2ac5f0351vdf4e49f7b6923ee2fc235c4c');
/**/

$x = 1;