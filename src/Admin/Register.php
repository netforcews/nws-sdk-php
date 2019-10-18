<?php namespace Nws\Admin;

trait Register
{
    /**
     * Registrar um novo inquilino e usuario admin.
     * 
     * @param array $inquilino
     * @param array $usuario
     * @return array
     */
    public function register($inquilino, $usuario)
    {
        $ret = $this->toJson($this->request('post', 'register', [
            'json' => [
                'inquilino' => $inquilino,
                'usuario' => $usuario,
            ],
        ]));

        if (!$ret['status']) {
            return null;
        }

        $res = $ret['resources'];

        return [
            'inquilino' => $this->newInstanceResult($res['inquilino']),
            'usuario'   => $this->newInstanceResult($res['usuario']),
        ];
    }
}
