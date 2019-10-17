<?php namespace NetForce\Sdk\Model;

class Relation
{
    /**
     * @var Model
     */
    protected $modelBase;

    /**
     * Construtor.
     * 
     * @param Model $model
     */
    public function __construct(Model $modelBase)    
    {
        $this->modelBase = $modelBase;
    }
}