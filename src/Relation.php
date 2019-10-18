<?php namespace Nws;

abstract class Relation
{
    /**
     * @var SdkClient
     */
    public $client;

    /**
     * @var Result
     */
    public $parent;

    /**
     * @param SdkClient $client
     * @param Result $parent
     */
    public function __construct(SdkClient $client, Result $parent)
    {
        $this->client = $client;
        $this->parent = $parent;
    }

    /**
     * Retorna os resultados.
     * 
     * @return mixed
     */
    abstract public function getResults();    
}
