<?php

/**
 * User: hitman
 * Date: 2019/8/20
 * Time: 2:33 PM
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use ModelTrait;

    protected $guarded = [];

    public function setPasswordAttribute($value)
    {
        if ($value) $this->attributes['password'] = bcrypt($value);
    }
}
