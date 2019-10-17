<?php namespace NetForce\Sdk\Models;

use NetForce\Sdk\Models\Utils\Model;

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
