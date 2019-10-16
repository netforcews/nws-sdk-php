<?php namespace NetForce\Sdk\Traits;

use NetForce\Sdk\Credentials;
use Illuminate\Support\Str;

trait PrepareRequest
{
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
        if (($uri == '') && (Str::is('*/', $base_uri))) {
            $base_uri = substr($base_uri, 0, -1);
        }
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
}