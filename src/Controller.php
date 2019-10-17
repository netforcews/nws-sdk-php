<?php namespace NetForce\Sdk;

use Exception;

class Controller
{
    /**
     * @var SdkClient
     */
    protected $client;    

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @param SdkClient $client
     * @param string $model
     * @param string $uri
     */
    public function __construct(SdkClient $client, $model, $uri)
    {
        $this->client = $client;
        $this->modelClass = $model;
        $this->uri = $uri;
    }

    /**
     * Listar recursos.
     * 
     * @param array $params
     * @return Collection
     */
    public function query(array $params = [])
    {
        $ret = $this->client->toJson($this->client->request('get', $this->uri('', $params)));

        $lista = array_map(function ($item) {
            return $this->newInstanceModel($item);
        }, $ret);

        return Collection::make($lista);
    }

    /**
     * Get um recurso pelo $id em params.
     * 
     * @param array $params
     * @return Model|null
     */
    public function get(array $params)
    {
        try {
            $ret = $this->client->request('get', $this->uri('{id}', $params));

            return $this->newInstanceModel($ret);
        } catch (Exception $e) {
            if ($e->getCode() == 400) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Criar um novo recurso.
     * 
     * @param Model|array $data
     * @param array $params
     * @return Model|null
     */
    public function create($data, array $params = [])
    {
        // Tratar params
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        $ret = $this->client->toJson($this->client->request('post', $this->uri('', $params), [
            'json' => $data,
        ]));

        if ($ret['status']) {
            return $this->newInstanceModel($ret['resource']);
        }

        return null;
    }

    /**
     * Atualizar um recurso pelo $id.
     * 
     * @param array $params
     * @param Model|array $data
     * @return Model|null
     */
    public function update($params, $data)
    {
        // Tratar params
        if ($data instanceof Model) {
            // Carregar alteracoes dos atributos
            $diff = $data->getDirty();        
            if (count($diff) == 0) {
                return $data;
            }

            $data = $diff;
        }

        $ret = $this->client->toJson($this->client->request('put', $this->uri('{id}', $params), [
            'json' => $data,
        ]));

        if ($ret['status']) {
            return $this->newInstanceModel($ret['resource']);
        }

        return null;
    }

    /**
     * Delete um recurso pelo $id.
     * 
     * @param array $params
     * @return bool
     */
    public function delete(array $params)
    {
        $ret = $this->client->toJson($this->client->request('delete', $this->uri('{id}', $params)));

        return $ret['status'];
    }

    /**
     * Criar nova instancia do model.
     * 
     * @param array $data
     * @return Model
     */
    protected function newInstanceModel($data = [])
    {
        $class = $this->modelClass;

        return new $class($this->client, $data);
    }

    /**
     * Tratar URI.
     * 
     * @param string $part
     * @param array $params
     */
    public function uri($part, $params = [])
    {
        $uri = $this->uri . $part;

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }
}