<?php

namespace App\Exceptions;


class UnauthorizedException extends Exception
{
    public function render($request){
        return $this->error("验证失败");
    }    
}
