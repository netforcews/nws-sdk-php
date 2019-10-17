<?php namespace NetForce\Sdk;

use Illuminate\Support\Str;
use NetForce\Sdk\Models\Utils\Controller;

trait ClientModel
{
    protected $models = [];

    /**
     * Retorna as requisições da requisição.
     * 
     * @return \NetForce\Sdk\Models\Utils\Controller
     */
    protected function toModel($model, $uri = null)
    {
        $uri = is_null($uri) ? $this->getCallerFunction() . '/' : $uri;

        $controller = new Controller($this, $model, $uri);

        return $controller;
    }

    /**
     * Retorna controller do model.
     * @return Controller
     */
    protected function getModel($model)
    {
        if (array_key_exists($model, $this->models)) {
            return $this->models[$model];
        }

        // Verificar se foi implementado um model (metodo)
        $method = lcfirst(Str::studly($model));
        if (!method_exists($this, $method)) {
            return null;
        }

        // Carregar relation
        $response = call_user_func_array([$this, $method], []);
        if (!$response instanceof Controller) {
            throw new \Exception("Invalid model controller [$model]");
        }

        return $this->models[$model] = $response;
    }

    /**
     * Get function name of caller.
     *
     * @return string
     */
    protected function getCallerFunction()
    {
        list($one, $two, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return $caller['function'];
    }
}
