<?php namespace NetForce\Sdk\Models\Utils;

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