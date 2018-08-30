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
            'email' => 'required|email|unique:users',	
            'password' => 'required|string|min:6|max:10',
			'id' => 'required|string|unique:users',
			'firstName' => 'required|string',
			'lastName'  => 'required|string',
			//'role' => 'required|string',
			'phone' => 'required|string',
			'cellphone' => 'required|string',
			'birthDate' => 'required|string',
			'city' => 'required|string',
			'status' => 'required|string',
			'address' => 'required|string',
			//'country' => 'required|string',
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
		$user->password = bcrypt($request->password);
        $user->id = $request->id;
		$user->firstName = $request->firstName;
		$user->lastName  = $request->lastName;
		$user->phone = $request->phone;
		$user->cellphone = $request->cellphone;
		$user->birthDate = $request->birthDate;
		$user->city = $request->city; 
		$user->status = $request->status;
		$user->address = $request->address;
        $user->save();
        return response([
            'status' => 'success',
            'data' => $user
		   ], 200);
		
    }
    public function getUser(Request $request){
	   	//$user = User::find(JWTAuth::attem);
		//$user = auth()->user();
	   	$validator = Validator::make($request->all(), [
			'id' => 'required|string',		
		]);
		if ($validator->fails()) {
	   		return response([
				'status' => 'error',
				'msg' => "id: ".$request->id
			]);
			//], 400);
		}
		try{
			$user = User::findOrFail($request->id);
		}
		catch(Exception $e){
			return response([
	            'status' => 'error',
	            'msg' => 'User not found'
	    	]);
		}
	    return response([
	            'status' => 'success',
	            'data' => $user
	    ]);
	}
	public function getAllUsers(Request $request){
		$users = User::all();
		return response([
			'status' => 'success',
			'data' => $users
		]);
	}
	public function deleteUser(Request $request){
		try{
			$user = User::findOrFail($request->id);
		}
		catch(Exception $e){
			return response([
	            'status' => 'error',
	            'msg' => 'User not found'
	    	]);
		}
		$user->delete();
		return response([
			'status' => 'success']);
	}
	public function updateUser(Request $request){
		/*$validator = Validator::make($request->all(), [
            //'email' => 'required|email|unique:users',	
            //'password' => 'required|string|min:6|max:10',
			'id' => 'required|string|unique:users',
			//'firstName' => 'required|string',
			//'lastName'  => 'required|string',
			//'role' => 'required|string',
			//'phone' => 'required|string',
			//'cellphone' => 'required|string',
			//'birthDate' => 'required|string',
			//'city' => 'required|string',
			//'status' => 'required|string',
			//'address' => 'required|string',
			//'country' => 'required|string',
        ]);
        if ($validator->fails()) {
           return response([
	                'status' => 'error',
	                'error' => 'invalid.credentials',
					'msg' => 'Invalid Credentials.'
				]);
	            //], 400);
		}*/
		try{
			$user = User::findOrFail($request->id);
		}
		catch(Exception $e){
			return response([
	            'status' => 'error',
	            'msg' => 'User not found'
	    	]);
		}
		$request = $request->user;
		$user->email = $request['email'];//$request->email;
        $user->id = $request['id'];
		$user->city = $request['city']; 
		$user->password = bcrypt($request['password']);
		$user->firstName = $request['firstName'];
		$user->lastName  = $request['lastName'];
		$user->phone = $request['phone'];
		$user->cellphone = $request['cellphone'];
		$user->birthDate = $request['birthDate'];
		$user->city = $request['city']; 
		$user->status = $request['status'];
		$user->address = $request['address'];
        $user->save();
        return response([
            'status' => 'success'
		   ], 200);
		
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
