<?php 

namespace App\Http\Controllers\Api;

use App\Exceptions\LoginFailedException;
use App\Http\Requests\Auth\SignupPhoneRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller 
{
    public function signupViaPhone(SignupPhoneRequest $request)
    {
        // nation_code, phone, password, code
        $data = [
            'nation_code' => $request->nation_code,
            'phone' => $request->phone,
            'password' => $request->password,
        ];

        $user = User::create($data);

        return $this->responseItem(true);
    }

    public function signupViaEmail(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $user = User::create($data);
        
        return $this->responseItem(true);
    }

    public function signinViaPhone(Request $request)
    {
        $user = User::wherePhone($request->phone)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            throw new LoginFailedException();
        }
        $token = $user->getToken(true);

        return $this->responseItem($token);
    }

    public function signinViaEmail(Request $request)
    {
        $user = User::whereEmail($request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            throw new LoginFailedException();
        }
        $token = $user->getToken(true);

        return $this->responseItem($token);
    }

    public function signin2fa(Request $request)
    {
        $user = User::getUserFromFa2Token($request->token);
        $result = $user->fa2verify($request->type, $request->code);
        if(!$result) throw new LoginFailedException();
        
        $token = $user->getToken();

        return $this->responseItem($token);
    }

    public function signout()
    {

    }
}