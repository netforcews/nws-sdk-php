<?php namespace NetForce\Sdk\Model;

use NetForce\Sdk\Collection;

class Query
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Construtor.
     * 
     * @param Model $model
     */
    public function __construct(Model $model)    
    {
        $this->model = $model;
    }

    /**
     * Retorna a coleção dos models pelos filtros.
     * 
     * @return Collection
     */
    public function get()
    {
        try {
            $client = $this->model->getClient();

            $ret = $client->toJson($client->request('get', ''));

            $lista = array_map(function ($item) {
                return $this->model->newModel($this->model->getEnvironment(), $item, true);
            }, $ret);

            return Collection::make($lista);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Retorna o primeiro item dos models pelos filtros.
     * 
     * @return Model|null
     */
    public function first()
    {
        $item = $this->get()->first();

        return $item;
    }
}