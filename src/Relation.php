<?php namespace Nws;

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