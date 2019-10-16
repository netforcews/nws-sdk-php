<?php
use NetForce\Sdk\Auth\AuthClient;
use NetForce\Sdk\Register\RegisterClient;

require __DIR__ . '/../vendor/autoload.php';

$reg = new RegisterClient([
    'environment' => RegisterClient::envSandbox,
]);

$auth = new AuthClient([
    'environment' => AuthClient::envSandbox,
    //'credentials' => [
    //    'key'    => '1234',
    //    'secret' => '4321',
    //],
]);

/*
$auth->setCredentials([
    'key'    => '1234',
    'secret' => '4321',
]);/**/

$x = $reg->register([
    'ns'       => 'teste',
    'nome'     => 'Inquilino de teste',
    'situacao' => 'atv',
], [
    'nome'     => 'Bruno Gonçalves',
    'email'    => 'xbugotech@gmail.com',
    'password' => '123123',
    'situacao' => 'atv',
]);

/*
$ret = $auth->login('bugotech@gmail.com', '12345678');

/*
$me = $auth->me();

$v = $me->nome;
$v = $me->email;
$v = $me->inquilino_id;
$v = $me->created_at;
/*

$r = $auth->logout();
/**/

$x = 1;