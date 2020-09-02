<?php

/**
 * User: hitman
 * Date: 2019/8/20
 * Time: 2:33 PM
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Hitmanv\Laverify\VerifyCode;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use PHPGangsta_GoogleAuthenticator;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use ModelTrait, HasApiTokens;

    const TYPE_TOKEN_USER = 0;
    const TYPE_TOKEN_2FA = 1;

    const TYPE_2FA_PHONE = 1;
    const TYPE_2FA_EMAIL = 2;
    const TYPE_2FA_TOTP = 3;

    const KEY_FA2 = 'user:fa2:';

    protected $guarded = [];

    public function setPasswordAttribute($value)
    {
        if ($value) $this->attributes['password'] = bcrypt($value);
    }

    public function fa2verify($type, $code)
    {
        if($type == self::TYPE_2FA_EMAIL) {
            return VerifyCode::verify(config('laverify.type.2fa'), $this->email, $code);       
        }
        if($type == self::TYPE_2FA_PHONE) {
            return VerifyCode::verify(config('laverify.type.2fa'), $this->phone, $code);
        }
        if($type == self::TYPE_2FA_TOTP) {
            $ga = new PHPGangsta_GoogleAuthenticator();
            return $ga->verifyCode($this->totp_secret, $code);
        }

        return false;
    }

    public function getToken($fa=false)
    {
        if($fa){
            $token = self::genFa2Token($this);
            $t = ['token' => $token, 'type' => self::TYPE_TOKEN_2FA];
        } else {
            $token = $this->createToken('api-token', ['*'])->plainTextToken;
            $t = ['token' => $token, 'type' => self::TYPE_TOKEN_USER];
        }

        return $t;
    }

    public static function genFa2Token($user)
    {
        $token = Str::random();
        
        Cache::put(self::KEY_FA2 . $token, $user->id, 300);

        return $token;
    }

    public static function getUserFromFa2Token($token)
    {
        $userId = Cache::get(self::KEY_FA2 . $token);
        $user = self::find($userId);

        return $user;
    }
}
