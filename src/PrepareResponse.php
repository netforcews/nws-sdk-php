<?php namespace Nws;

use Exception;
use Psr\Http\Message\ResponseInterface;

trait PrepareResponse
{
    /**
     * Criar nova instancia do result.
     * 
     * @param array $data
     * @param string|null $result
     * @return Result
     */
    protected function newInstanceResult($data = [], $result = null)
    {
        $result = empty($result) ? '\Nws\Result' : $result;

        return new $result($this, $data);
    }
    
    /**
     * @param ResponseInterface $response
     * @return null|array
     */
    public function toJson(ResponseInterface $response)
    {
        $json = json_decode($response->getBody(), true);
        if (is_null($json)) {
            $message = trim($response->getBody());
            throw new Exception($message);
        }

        return $json;
    }

    /**
     * Test if error.
     *
     * @param ResponseInterface $response
     * @return bool
     * @throws \Exception
     */
    protected function testResponseError(ResponseInterface $response)
    {
        // Verificar error http
        if (!$response->getStatusCode() == 200) {
            throw new Exception("Error response: " . $response->getStatusCode());
        }

        // Verificar error via json
        $json = json_decode($response->getBody());
        if (is_null($json)) {
            return true;
        }

        if (!isset($json->error)) {
            return true;
        }

        $message = isset($json->error->message) ? $json->error->message : '???';
        $code = isset($json->error->code) ? $json->error->code : 0;

        // Verificar se tem erros de atributos
        if (isset($json->error->errors)) {
            $info = '';
            foreach ((array)$json->error->errors as $attr => $msgs) {
                $info .= " - $attr:\r\n";
                foreach ($msgs as $msg) {
                    $info .= "   - $msg\r\n";
                }
            }

            $message = sprintf("%s\r\n%s", $message, $info);
        }

        throw new Exception($message, $code);
    }
}