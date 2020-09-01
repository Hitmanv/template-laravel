<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;

class Exception extends \Exception
{
    use ResponseTrait;

    public function render($request)
    {
        return $this->error($this->message, $this->code);
    }
}
