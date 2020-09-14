<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);


beforeEach(function(){
    $this->email = "hitmanvi@qq.com";
    $this->nation_code = "86";
    $this->phone = "13333333333";
    $this->password = "123456";
    $this->wrong_password = "12345";
});

it("can signup via email", function(){
    $resp = $this->json('POST', '/api/signup/email', ['email' => $this->email, 'password' => $this->password]);
    assertOk($this, $resp);

    $user = User::whereEmail($this->email)->first();
    assertUser($this, $user);
});

it("can signup via phone", function(){
    $resp = $this->json('POST', '/api/signup/phone', ['nation_code' => $this->nation_code, 'phone' => $this->phone, 'password' => $this->password]);
    assertOk($this, $resp);

    $user = User::wherePhone($this->phone)->first();
    assertUser($this, $user);
});

it("can signin via email", function(){
    User::create(['email' => $this->email, 'password' => $this->password]);

    $resp = $this->json('POST', '/api/signin/email', ['email' => $this->email, 'password' => $this->password]);
    $data = $resp->getData(true);

    $this->assertTrue(isset($data['data']['token']), '没有返回token');
    $this->assertEquals($data['data']['type'], User::TYPE_TOKEN_2FA, "token类型不符");
});

it("can signin via phone", function(){
    User::create(['phone' => $this->phone, 'password' => $this->password]);
    $resp = $this->json('POST', '/api/signin/phone', ['phone' => $this->phone, 'password' => $this->password]);
    $resp->assertStatus(200);

    $data = $resp->getData(true);

    $this->assertTrue(isset($data['data']['token']));
    $this->assertEquals($data['data']['type'], User::TYPE_TOKEN_2FA, "token类型不符");
});

it("can't signin via email with wrong password", function(){
    User::create(['email' => $this->email, 'password' => $this->password]);
    $resp = $this->json('POST', '/api/signin/email', ['email' => $this->email, 'password' => $this->wrong_password]);
    $data = $resp->getData(true);
    
    $this->assertNotEquals($data['code'], 0);
});

it("can't signin via phone with wrong password", function(){
    User::create(['phone' => $this->phone, 'password' => $this->password]);
    $resp = $this->json('POST', '/api/signin/phone', ['phone' => $this->phone, 'password' => $this->wrong_password]);
    $data = $resp->getData(true);

    $this->assertNotEquals($data['code'], 0);
});

it("can signin via 2fa totp", function(){
    $ga = new PHPGangsta_GoogleAuthenticator;
    $user = User::create(['phone' => $this->phone, 'password' => $this->password, 'totp_secret' => $ga->createSecret()]);

    $resp = $this->json('POST', '/api/signin/phone', ['phone' => $this->phone, 'password' => $this->password]);
    $data = $resp->getData(true);
    
    $resp = $this->post('/api/signin/2fa', ['token' => $data['data']['token'], 'code' => $ga->getCode($user->totp_secret), 'type'=>User::TYPE_2FA_TOTP]);
    $data = $resp->getData(true);

    $this->assertTrue(isset($data['data']['token']));
    $this->assertEquals($data['data']['type'], User::TYPE_TOKEN_USER, "token类型不符");
});

it("can't signin via 2fa totp without correct code", function(){
    $ga = new PHPGangsta_GoogleAuthenticator;
    $user = User::create(['phone' => $this->phone, 'password' => $this->password, 'totp_secret' => $ga->createSecret()]);

    $resp = $this->json('POST', '/api/signin/phone', ['phone' => $this->phone, 'password' => $this->password]);
    $data = $resp->getData(true);
    
    $resp = $this->post('/api/signin/2fa', ['token' => $data['data']['token'], 'code' => '', 'type'=>User::TYPE_2FA_TOTP]);
    $data = $resp->getData(true);

    $this->assertNotEquals($data['code'], 0);
});

it("can access user content with correct token", function(){
    $user = User::create(['phone' => $this->phone, 'password' => $this->password]);
    $token = $user->getToken();
    
    $resp = $this->withHeaders(['Authorization' => 'Bearer ' . $token['token']])->json('GET', '/api/auth/test');
    $data = $resp->getData(true);

    $this->assertEquals($data['code'], 0);
});

it("can't access user content without correct token", function(){
    $user = User::create(['phone' => $this->phone, 'password' => $this->password]);
    $token = $user->getToken();
    
    $resp = $this->withHeaders(['Authorization' => 'Bearer abcd' ])->json('GET', '/api/auth/test');
    $data = $resp->getData(true);

    $this->assertNotEquals($data['code'], 0);
});


function assertOk($t, $resp){
    $data = $resp->getData(true);
    $t->assertEquals($data['code'], 0, "request success");
}

function assertUser($t, $user){
    $t->assertNotNull($user, "user not exists");
    $t->assertTrue(Hash::check($t->password, $user->password));
}