<?php namespace NetForce\Sdk\Admin;

use NetForce\Sdk\SdkClient;

class AdminClient extends SdkClient
{
    use Auth;
    use Register;

    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => 'http://localhost/apps/admin/public/',
    ];
}