<?php namespace Nws\Admin\Models;

use Nws\BelongsTo;

class Usuario extends \Nws\Result
{
    /**
     * Lookup inquilino.
     * @return BelongsTo
     */
    protected function inquilino()
    {
        return $this->belongsTo();
    }
}
