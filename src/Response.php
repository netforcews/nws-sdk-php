<?php namespace NetForce\Sdk;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var SdkClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $original = [];

    /**
     * @var array
     */
    protected $relation = [];

    /**
     * @var string|null
     */
    protected $_refUri;

    /**
     * @var string
     */
    protected $_uriBase = '';

    /**
     * @param SdkClient $client
     * @param $data
     */
    public function __construct(SdkClient $client, $data = null, $refUri = null)
    {
        $this->client = $client;
        $this->_refUri = $refUri;

        $this->setRawAttributes($data);
    }

    /**
     * Atribuir dados.
     * @param array $data
     * @param bool $original
     */
    public function setRawAttributes($data, $original = false)
    {
        if (!is_null($data)) {
            if ($data instanceof ResponseInterface) {
                $data = $this->client->toJson($data);
            }
        }

        $this->data = (array)$data;

        if ($original) {
            $this->original = $this->data;
        }

        return $this;
    }

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // Carregar valor original
        $data_value = $this->getData($key, $default);

        // Verificar se tem mutator
        if ($this->hasAttrMutator($key)) {
            return $this->getAttrMutator($key, $data_value);
        }

        // Verificar se eh um relation
        $value = $this->getRelation($key, $data_value);
        if (!is_null($value)) {
            return $value;
        }

        // Carregar data
        return $data_value;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getData($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * @return SdkClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->data as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @return bool
     */
    public function isDirty()
    {
        $dirty = $this->getDirty();

        return count($dirty) > 0;
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->data;

        return $this;
    }    

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function getAttrMutator($key, $value)
    {
        $method = sprintf('get%sAttr', Str::studly($key));
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }

        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function hasAttrMutator($key)
    {
        $method = sprintf('get%sAttr', Str::studly($key));

        return method_exists($this, $method);
    }

    /**
     * @param $key
     * @param $value
     * @return ResponseObject|null
     */
    protected function getRelation($key, $value)
    {
        // Verificar se foi implementado um relation
        $method = lcfirst(Str::studly($key));
        if (!method_exists($this, $method)) {
            return null;
        }

        // Verificar se relation jah foi carregado
        if (array_key_exists($key, $this->relation)) {
            return $this->relation[$key];
        }

        // Verificar se deve carregar com o sufixo _id
        if (is_null($value)) {
            $value = $this->getData(sprintf('%s_id', $key));
        }

        // Carregar relation
        $response = call_user_func_array([$this, $method], [$value]);
        if (!$response instanceof Response) {
            throw new \Exception("Invalid relation [$key]");
        }

        return $this->relation[$key] = $response;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        Arr::set($this->data, $key, $value);
    }

    /**
     * Load ref.
     */
    protected function loadRef()
    {
        // Verificar se ref uri foi definido
        if (is_null($this->_refUri)) {
            return;
        }

        // Verificar se jah foi carregado
        if (count($this->data) > 0) {
            return;
        }

        // Carregar referencia
        $this->data = $this->client->responseJson($this->client->request('get', $this->_refUri));
    }

    /**
     * Retorna uma data e hora.
     *
     * @return null|Carbon
     */
    protected function toDate($value)
    {
        if (!is_null($value)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value);
        }

        return $value;
    }

    /**
     * Update values.
     *
     * @param array $values
     * @return bool
     */
    protected function toUpdate($resource, array $values, $except = [])
    {
        $data = Arr::except($values, $except);

        $ret = $this->client->responseJson($this->client->request('put', $this->client->uri($resource, [$this->id]), [
            'json' => $data,
        ]));

        if ($ret['success']) {
            $this->data = array_merge([], $this->data, $data);
        }

        return $ret['success'];
    }

    /**
     * Delete document.
     *
     * @return bool
     */
    protected function toDelete($resource)
    {
        $ret = $this->client->responseJson($this->client->request('delete', $this->client->uri($resource, [$this->id])));

        return $ret['success'];
    }

    /**
     * @return null|Carbon
     */
    protected function getCreatedAtAttr($value)
    {
        return $this->toDate($value);
    }

    /**
     * @return null|Carbon
     */
    protected function getUpdatedAtAttr($value)
    {
        return $this->toDate($value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}
