<?php namespace NetForce\Sdk\Model;

use Exception;
use NetForce\Sdk\Response;
use NetForce\Sdk\SdkClient;

class Model extends Response
{
    const envProduction = SdkClient::envProduction;
    const envSandbox    = SdkClient::envSandbox;

    /**
     * @var array
     */
    protected $endpoints = [
        'production' => '',
        'sandbox'    => '',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The environment of model.
     *
     * @var string
     */
    protected $environment = SdkClient::envProduction;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Construtor.
     * 
     * @param array $data
     * @param array $env
     * @param bool $exists
     */
    public function __construct($data = [], $env = null, $exists = false)
    {
        parent::__construct($this->createClient($env), $data);

        $this->environment = $env;

        $this->exists = $exists;
    }

    /**
     * Procurar uma lista de recursos.
     * 
     * @param string $env
     * @return Query
     */
    public static function query($env = null)
    {
        return new Query(static::newModel($env));
    }

    /**
     * Procurar um novo recursos pelo id.
     * 
     * @param string $id
     * @param string $env
     * @return Model|null
     */
    public static function find($id, $env = null)
    {
        try {
            $model = static::newModel($env);
    
            $ret = $model->getClient()->request('get', $id);
    
            $model->setRawAttributes($ret, true);

            $model->exists = true;
    
            return $model;
        } catch (Exception $e) {
            if ($e->getCode() == 400) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Incluir/Salvar recurso.
     */
    public function save()
    {
        if ($this->exists) {
            $saved = $this->isDirty() ? $this->executeUpdate() : true;
        } else {
            $saved = $this->executeInsert();
        }

        if ($saved) {
            $this->syncOriginal();
        }

        return $saved;
    }

    /**
     * Excluir recurso.
     */
    public function delete()
    {
        // Se não foi carregado um objeto deve ignorar operação
        if (! $this->exists) {
            return;
        }

        return $this->executeDelete();
    }

    /**
     * Criar novo model.
     * 
     * @param string $env
     * @param array $data
     * @param bool $exists
     * @return Model
     */
    public static function newModel($env, $data = [], $exists = false)
    {
        $class = static::class;

        return new $class($data, $env, $exists);
    }

    /**
     * Criar SDK Client.
     * 
     * @return SdkClient
     */
    protected function createClient($env)
    {
        $env = is_null($env) ? SdkClient::envProduction : $env;

        $client = new SdkClient([
            'environment' => $env,
        ]);
        $client->setEndpoints($this->endpoints[SdkClient::envSandbox], $this->endpoints[SdkClient::envProduction]);

        return $client;
    }

    /**
     * Incluir novo recurso.
     */
    protected function executeInsert()
    {
        try {
            $client = $this->getClient();

            $attributes = $this->data;

            $ret = $client->toJson($client->request('post', '', [
                'json' => $attributes,
            ]));

            if ($ret['status']) {
                $this->setRawAttributes($ret['resource']);

                $this->exists = true;
            }
    
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Atualizar recurso.
     */
    protected function executeUpdate()
    {
        // Se não foi carregado um objeto deve ignorar operação
        if (! $this->exists) {
            return;
        }

        // Carregar alteracoes dos atributos
        $dirty = $this->getDirty();        
        if (count($dirty) == 0) {
            return true;
        }

        try {
            $client = $this->getClient();

            $id = $this->getKey();
            
            $ret = $client->toJson($client->request('put', $id, [
                'json' => $dirty,
            ]));

            if ($ret['status']) {
                $this->setRawAttributes($ret['resource']);
            }
    
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Excluir recurso.
     */
    protected function executeDelete()
    {
        // Se não foi carregado um objeto deve ignorar operação
        if (! $this->exists) {
            return;
        }

        try {
            $client = $this->getClient();

            $id = $this->getKey();
            
            $ret = $client->toJson($client->request('delete', $id));

            if ($ret['status']) {
                $this->exists = false;
            }

    
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->get($this->getKeyName());
    }

    /**
     * Get environment of model.
     * 
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    //protected function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
}