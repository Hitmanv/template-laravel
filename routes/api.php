<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Api'], function(){
    Route::post('/signup/phone', 'AuthController@signupViaPhone'); // 手机注册
    Route::post('/signup/email', 'AuthController@signupViaEmail'); // 邮箱注册
    Route::post('/signin/phone', 'AuthController@signinViaPhone'); // 手机登录
    Route::post('/signin/email', 'AuthController@signinViaEmail'); // 邮箱登录
    Route::post('/signin/second', 'AuthController@signinSecond'); // 二次认证登录
    Route::post('/signout', 'AuthController@signout'); // 退出登录
    Route::post('/forget-password/reset'); // 忘记密码-重置密码
    
    // 发送验证码
    Route::post('/verify-code/phone');
    Route::post('/verify-code/email');
});
