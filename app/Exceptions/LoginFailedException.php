<?php

namespace App\Exceptions;

class LoginFailedException extends Exception
{
    public function render($request)
    {
        return $this->error("登录失败");
    }    
}
