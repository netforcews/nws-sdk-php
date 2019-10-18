<?php namespace Nws;

use Illuminate\Support\Str;

/**
 * Estrutura de requisição do client.
 * 
 * @property \GuzzleHttp\Client $client
 * @property array $headers
 * @method mixed config($key, $default = null)
 * @method Result newInstanceResult($data = [], $result = null)
 */
trait ClientRequest
{
    /**
     * Action: list
     */
    protected function actionList($function, $args)
    {
        $result = $function['result'];
        $uri    = $this->actionsUri($function['uri'], $args);

        $ret = $this->toJson($this->request('get', $uri));

        $lista = array_map(function ($item) use ($result) {
            return $this->newInstanceResult($item, $result);
        }, $ret);

        return Collection::make($lista);
    }

    /**
     * Action: get
     */
    protected function actionGet($function, $args)
    {
        $result = $function['result'];
        $uri    = $this->actionsUri($function['uri'], $args);

        try {
            $ret = $this->request('get', $uri);

            return $this->newInstanceResult($ret, $result);
        } catch (Exception $e) {
            if ($e->getCode() == 400) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Action: create
     */
    protected function actionCreate($function, $args)
    {
        // Tratar params
        if ($args instanceof Result) {
            $args = $args->toArray();
        }

        $result = $function['result'];
        $uri    = $this->actionsUri($function['uri'], $args);

        $ret = $this->toJson($this->request('post', $uri, [
            'json' => $args,
        ]));

        if ($ret['status']) {
            return $this->newInstanceResult($ret['resource'], $result);
        }

        return null;
    }

    /**
     * Action: update
     */
    protected function actionUpdate($function, $args)
    {
        // Tratar params
        if ($args instanceof Result) {
            // Carregar alteracoes dos atributos
            $diff = $args->getDirty();        
            if (count($diff) == 0) {
                return $args;
            }

            $args = $diff;
        }

        $result = $function['result'];
        $uri    = $this->actionsUri($function['uri'], $args);

        $ret = $this->toJson($this->request('put', $uri, [
            'json' => $args,
        ]));

        if ($ret['status']) {
            return $this->newInstanceResult($ret['resource'], $result);
        }

        return null;
    }

    /**
     * Action: delete
     */
    protected function actionDelete($function, $args)
    {
        $uri = $this->actionsUri($function['uri'], $args);

        $ret = $this->toJson($this->request('delete', $uri));

        return $ret['status'];
    }

    /**
     * Tratar URI.
     * 
     * @param string $part
     * @param array $params
     */
    protected function actionsUri($uri, $args = [])
    {
        foreach ($args as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }

    /**
     * Requisição Asincrona.
     * 
     * @param $method
     * @param string $uri
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function requestAsync($method, $uri = '', array $options = [])
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
    public function request($method, $uri = '', array $options = [])
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
        $this->prepareRequest($httpMethod, $uri, $options);

        $response = $this->client->$clientMethod($httpMethod, $this->preparaUriEnd($uri), $options);

        $this->testResponseError($response);

        return $response;
    }

    /**
     * Retorna as requisições da requisição.
     * 
     * @return array
     */
    protected function getRequestHeaders()
    {
        $custom_headers = $this->config('http.headers', []);
        $access_headers = $this->getAccessHeaders();

        $ret = array_merge($this->headers, $custom_headers, $access_headers);

        return $ret;
    }

    /**
     * Retorna o header para acesso a api.
     * 
     * @return array
     */
    protected function getAccessHeaders()
    {
        $credentials = Credentials::get();
        if (is_null($credentials) || (!is_array($credentials))) {
            return [];
        }

        if (array_key_exists('access_token', $credentials)) {
            return ['Access-Token' => $credentials['access_token']];
        }

        if (array_key_exists('key', $credentials) && array_key_exists('secret', $credentials)) {
            return [
                'Key'    => $credentials['key'],
                'Secret' => $credentials['secret'],
            ];
        }

        return [];
    }

    /**
     * Tratar options request.
     * 
     * @param string $method
     * @param string $uri
     * @param array $options
     */
    protected function prepareRequest($method, $uri, &$options)
    {
        // Base URI
        $base_uri = $this->getEndpoint();
        $base_uri = ($uri == '') ? $this->preparaUriEnd($base_uri) : $base_uri;
        $options['base_uri'] = $base_uri;

        // Send XDebug
        $xdebug = $this->config('xdebug', false);
        if ($xdebug !== false) {
            if (!isset($options['query'])) {
                $options['query'] = [];
            }
            $options['query']['XDEBUG_SESSION_START'] = $xdebug;
        }

        // Headers
        $options['headers'] = $this->getRequestHeaders();
    }

    /**
     * Tratar uri sem barra no final
     * 
     * @param string $uri
     * @return string
     */
    protected function preparaUriEnd($uri)
    {
        // Verificar se deve remover ultima / no final da string
        if (Str::is('*/', $uri)) {
            $uri = substr($uri, 0, -1);
        }

        return $uri;
    }
}
