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

	public function register(Request $request){
		 $validator = Validator::make($request->all(), [
		 	'name' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',	
            'password' => 'required|string|min:6|max:10',
			'id' => 'required|string|unique:users',
			'firstName' => 'required|string',
			'lastName'  => 'required|string',
			//'role'
			'phone' => 'required|string',
			'cellphone' => 'required|string',
			'birthDate' => 'required|string',
			'city' => 'required|string',
			'status' => 'required|string',
			'address' => 'required|string',
			//'country'
        ]);

        if ($validator->fails()) {
           return response([
	                'status' => 'error',
	                'error' => 'invalid.credentials',
					'msg' => 'Invalid Credentials.'
				]);
	            //], 400);
        }
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->save();
        return response([
            'status' => 'success',
            'data' => $user
           ], 200);
    }
    public function getUser(Request $request){
	   //$user = User::find(JWTAuth::attem);
		$user = auth()->user();
	    return response([
	            'status' => 'success',
	            'data' => $user
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
        return response([
         'status' => 'success'
        ]);
    }
}
