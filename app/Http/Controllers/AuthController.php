<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Validator;
use App\Http\Requests\RegisterFormRequest;

class AuthController extends Controller
{
	//Peticion de loggeo. Se necesita el Email y la contraseña y se regresa un mensaje con el estatus y la info
	//Si el estado es Success se regresa el toke
	//Si el estado es Error se regresa un mensaje con el error.
    public function login(Request $request){
	    $credentials = $request->only('email', 'password');
	    $token = JWTAuth::attempt($credentials);
	    if ( ! $token = JWTAuth::attempt($credentials)) {
	            return response([
	                'status' => 'error',
	                'msg' => 'Credenciales Inválidas'
	            ]);
	    }
	    return response([
	            'status' => 'success',
	            'token' => $token
	        ]);
	}

	//Funcion para cerrar sesión e invalidar el token
	public function logout(){
	    JWTAuth::invalidate();
	    return response([
	            'status' => 'success',
	            'msg' => 'Logged out Successfully.'
	        ], 200);
	}

	//Funcion para refrescar el token. Toma el antiguo token y genera uno nuevo, luego lo regresa.
	public function refresh(){
		$token = JWTAuth::getToken();
		$newToken = JWTAuth::refresh($token);
        return response([
		 'status' => 'success',
		 'data' => $newToken
        ]);
    }
}
