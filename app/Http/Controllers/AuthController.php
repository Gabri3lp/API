<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Validator;
use App\Http\Requests\RegisterFormRequest;

class AuthController extends Controller
{
    public function login(Request $request){
	    $credentials = $request->only('email', 'password');
	    $token = JWTAuth::attempt($credentials);
	    if ( ! $token = JWTAuth::attempt($credentials)) {
	            return response([
	                'status' => 'error',
	                'error' => 'invalid.credentials',
	                'message' => 'Invalid Credentials'
	            ]);
	    }
	    return response([
	            'status' => 'success',
	            'token' => $token
	        ]);
	}

	public function logout(){
	    JWTAuth::invalidate();
	    return response([
	            'status' => 'success',
	            'msg' => 'Logged out Successfully.'
	        ], 200);
	}


	public function refresh(){
		$token = JWTAuth::getToken();
		$newToken = JWTAuth::refresh($token);
        return response([
		 'status' => 'success',
		 'data' => $newToken
        ]);
    }
}
