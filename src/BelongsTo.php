<?php namespace Nws;

use Illuminate\Support\Str;

class BelongsTo extends Relation
{
    /**
     * Nome do attributo do parent com o ID do recurso estrangeiro.
     * Ex,: ..._id
     * 
     * @var string
     */
    public $foreignKey = '';

    /**
     * Nome do attributo ID no recurso estrangeiro.
     * 
     * @var string
     */
    public $localKey = 'id';

    /**
     * Nome da função ou callback para carregar.
     * 
     * @var string
     */
    protected $loader = '';

    /**
     * @param SdkClient $client
     * @param Result $parent
     * @param string $foreignKey
     * @param string $loader
     * @param string $localKey
     */
    public function __construct(SdkClient $client, Result $parent, $foreignKey, $loader = null, $localKey = 'id')
    {
        parent::__construct($client, $parent);

        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->loader = is_null($loader) ? $this->makeLoaderString() : $loader;
    }

    /**
     * Retorna os resultados.
     * 
     * @return mixed
     */
    public function getResults()
    {
        if (is_string($this->loader)) {
            return $this->getLoaderCallback();
        }

        if (is_callable($this->loader)) {

            $args = [
                $this->localKey => $this->getForeignKeyValue(),
            ];

            return call_user_func_array($this->loader, [$this, $args]);
        }

        return null;
    }

    /**
     * Executar callback no mesmo client.
     * 
     * @return Result|mixed
     */
    protected function getLoaderCallback()
    {
        $args = [
            $this->localKey => $this->getForeignKeyValue(),
        ];

        return call_user_func_array([$this->client, $this->loader], [$args]);
    }

    /**
     * Retorna o valor do lookup no parent.
     * 
     * @return string
     */
    public function getForeignKeyValue()
    {
        return $this->parent->get($this->foreignKey);        
    }

    /**
     * Gerar string loader.
     * 
     * @return string
     */
    protected function makeLoaderString()
    {
        $resource = $this->foreignKey;;

        if (Str::is('*_id', $resource)) {
            $resource = substr($resource, 0, -3);
        }

        $resource = Str::ucfirst($resource);

        return sprintf('get%s', $resource);
    }
}
