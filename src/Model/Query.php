<?php namespace NetForce\Sdk\Model;

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
}