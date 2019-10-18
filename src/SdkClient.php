<?php namespace Nws;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SdkClient
{
    use ClientRequest;
    use PrepareResponse;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Lista de endpoints do serviço.
     * 
     * @var array
     */
    protected $endpoints = [];

    /**
     * Lista de funções do serviço.
     * 
     * @var array
     */
    protected $functions = [];

    /**
     * @var array
     */
    protected $headers = [
        'User-Agent'    => '???',
        'Cache-Control' => 'no-cache',
        'Accept'        => 'application/json',
    ];

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;

        Credentials::setFromConfig($this->config);

        $this->headers['User-Agent'] = 'nws/1.0.0 ' . \GuzzleHttp\default_user_agent();

        $this->client = new Client([]);

        $this->loadDefinitions();
    }

    /**
     * @param string|array $key
     * @param null $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        if (is_array($key) && is_null($default)) {
            $this->config = array_merge($this->config, $key);
            return true;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Retorna o endpoint pelo ambiente.
     *
     * @return string
     * @throws \Exception
     */
    protected function getEndpoint()
    {
        // Verificar se foi informado o endpoitn explicito
        $url = $this->config('endpoint');
        if (!is_null($url)) {
            return $url;
        }

        $env = $this->config('environment', 'production');
        if (!array_key_exists($env, $this->endpoints)) {
            throw new \Exception("Environment api client invalid [$env]");
        }

        return $this->endpoints[$env];
    }

    /**
     * Carregar definições do serviço.
     */
    protected function loadDefinitions()
    {
        $class = get_called_class();

        // Carregar definições dos serviços
        $services = __DIR__ . '/data/services.php';
        if (! file_exists($services)) {
            throw new \Exception("Definicoes dos servicos nao foram encontradas");
        }
        $services = require $services;

        // Procurar servico
        if (! array_key_exists($class, $services)) {
            throw new \Exception("Definicoes do servico $class nao foram encontradas");
        }

        $def = $services[$class];

        $this->endpoints = $def['endpoints'];
        $this->functions = $def['functions'];
    }

    /**
     * Executar função.
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     */
    protected function callFunction($name, $args)
    {
        $func = $this->functions[$name];
        
        $method = 'action' . Str::studly($func['action']);
        if (! method_exists($this, $method)) {
            throw new \Exception("Action nao implementada: " . $func['action']);
        }

        return call_user_func_array([$this, $method], [$func, $args]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getModel($name);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        // Procurar por funções
        if (array_key_exists($name, $this->functions)) {
            $args = (count($arguments) >= 1) ? $arguments[0] : [];
            return $this->callFunction($name, $args);
        }

        throw new \Exception("Funcao $name nao definida");
    }
}