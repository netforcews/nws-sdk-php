<?php namespace NetForce\Sdk;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use NetForce\Sdk\Traits\PrepareRequest;
use NetForce\Sdk\Traits\PrepareResponse;

class SdkClient
{
    use PrepareRequest;
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
     * @var array
     */
    protected $endpoints = [
        'production' => '', // http://api.com/{version}
        'sandbox'    => '', // http://api.sandbox.api.com/{version}
    ];

    /**
     * @var array
     */
    protected $headers = [
        'Cache-Control' => 'no-cache',
        'Accept'        => 'application/json',
    ];

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;

        $this->client = new Client([
            'base_uri' => $this->getEndPoint(),
        ]);
    }

    /**
     * @param $key
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
    protected function getEndPoint()
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
     * Requisição Asincrona.
     * 
     * @param $method
     * @param string $uri
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function requestAsync($method, $uri = '', array $options = [])
    {
        return $this->clientRequest(__FUNCTION__, $method, $uri, $options);
    }

    /**
     * Requisição Sincrona.
     * 
     * @param $method
     * @param string $uri
     * @param array $options
     * @return mixed
     */
    protected function request($method, $uri = '', array $options = [])
    {
        return $this->clientRequest(__FUNCTION__, $method, $uri, $options);
    }

    /**
     * Executa requisição.
     * 
     * @param $clientMethod
     * @param $httpMethod
     * @param string $uri
     * @param array $options
     * @return mixed
     */
    protected function clientRequest($clientMethod, $httpMethod, $uri = '', array $options = [])
    {
        $this->prepareRequest($httpMethod, $options);

        $response = $this->client->$clientMethod($httpMethod, $uri, $options);

        $this->testResponseError($response);

        return $response;
    }

    /**
     * Retorna o objeto response.
     * @return Response
     */
    protected function toResponse($response)
    {
        return new Response($this, $response);
    }
}
