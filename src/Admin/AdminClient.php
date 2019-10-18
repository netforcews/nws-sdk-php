<?php namespace Nws\Admin;

/**
 * Client used to interact with NetForce Admin
 * 
 * @method \Nws\Collection listInquilinos($args = [])
 * @method \Nws\Admin\Models\Inquilino getInquilino($args = [])
 * @method \Nws\Admin\Models\Inquilino createInquilino($args = [])
 * @method \Nws\Admin\Models\Inquilino updateInquilino($args = [])
 * @method bool deleteInquilino($args = [])
 * @method \Nws\Collection listUsuarios($args = [])
 * @method \Nws\Admin\Models\Usuario getUsuario($args = [])
 * @method \Nws\Admin\Models\Usuario createUsuario($args = [])
 * @method \Nws\Admin\Models\Usuario updateUsuario($args = [])
 * @method bool deleteUsuario($args = [])
 * @method \Nws\Collection listUsuarioTokens($args = [])
 * @method \Nws\Admin\Models\UsuarioToken getUsuarioToken($args = [])
 * @method \Nws\Admin\Models\UsuarioToken createUsuarioToken($args = [])
 * @method \Nws\Admin\Models\UsuarioToken updateUsuarioToken($args = [])
 * @method bool deleteUsuarioToken($args = [])
 */
class AdminClient extends \Nws\SdkClient
{
    use Auth;
    use Register;
}