<?php

namespace App\Http\Controllers\Api;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseTrait;
}
