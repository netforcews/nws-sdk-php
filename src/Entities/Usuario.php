<?php namespace NetForce\Sdk\Entities;

use NetForce\Sdk\Model\Model;

class Usuario extends Model
{
    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => 'http://localhost/apps/admin/public/usuarios/',
    ];
}
