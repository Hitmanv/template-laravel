<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignupPhoneRequest extends FormRequest
{
    public function rules()
    {
        // 必要参数
        // 手机格式
        // 验证码？
        return [
            'phone' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '手机没填',
        ];
    }
}
