<?php

namespace App\Http\Controllers\Api;

use Hitmanv\Laverify\VerifyCode;
use Illuminate\Http\Request;

class VerifyCodeController extends Controller 
{
    const TYPE_REGISTER = 1;
    const TYPE_FORGET_PASSWORD = 2;

    public function email(Request $request)
    {
        $type = $request->get('type');
        $email = $request->get('email');        
        $code = VerifyCode::gen($type, $email);
        // TODO: 发送邮件
        return $this->responseItem(true);
    }

    public function phone(Request $request)
    {
        $type = $request->get('type');
        $nationCode = $request->get('nation_code');
        $phone = $request->get('phone');
        $code = VerifyCode::gen($type, $phone);
        // TODO: 发送短信
        return $this->responseItem(true);
    }

}