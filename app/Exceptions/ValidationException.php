<?php

namespace App\Exceptions;


class ValidationException extends Exception
{
    
    protected $message = "验证码错误";
    protected $code = 10001;

    public function __construct(\Illuminate\Validation\ValidationException $ex)
    {
        $msg = "";
        foreach($ex->errors() as $k => $v) {
            $msg .= collect($v)->join("\n");
        }
        $this->message = $msg;
    }
}
