<?php namespace NetForce\Sdk;

class Credentials
{
    /**
     * Informacoes de credenciais.
     * 
     * @var array
     */
    protected static $values = [];

    /**
     * Retorna as credenciais do contexto.
     * @return array
     */
    public static function get()
    {
        return static::$values;
    }

    /**
     * Altera as credenciais do contexto.
     * @param array $credentials
     */
    public static function set($credentials)
    {
        static::$values = $credentials;
    }

    /**
     * Altera as credenciais do contexto pelas configurações do client.
     * @param array $config
     */
    public static function setFromConfig($config)
    {
        if (! array_key_exists('credentials', $config)) {
            return;
        }
        $credentials =  $config['credentials'];

        static::set($credentials);
    }
}