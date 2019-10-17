<?php namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestBase extends BaseTestCase
{
    /**
     * Envia mesagem para o console.
     * 
     * @param string $msg
     */    
    protected function info($msg)
    {
        fwrite(STDOUT, $msg . "\n");
    }
}