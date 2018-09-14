<?php


namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Validator;
class UsersController extends Controller
{
    public function currentUser(){
		$user = auth()->user();
		return response([
			'data' => $user
		]);
	}
	public function signup(Request $request){
		$validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',	
            'password' => 'required|string|min:6|max:10',
			'id' => 'required|string|unique:users',
			'firstName' => 'required|string',
			'lastName'  => 'required|string',
			'role.id' => 'required|exists:roles,id',
			'phone' => 'required|string',
			'cellphone' => 'required|string',
			'birthDate' => 'required|date',
			'city' => 'required|string',
			'status' => 'required|string',
			'address' => 'required|string',
			//'country' => 'required|string',
		]);
		
        if ($validator->fails()) {
           return response([
	                'status' => 'error',
					'msg' => $validator->messages()->all()[0]
				]);
		}
		
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		if($currentUser['role']['id'] == '3' 
			||	$currentUser['role']['id'] == '2' 
			&& ($request['role']['id'] == '2'  || $request['role']['id'] == '1')){
			return response([
				'status' => 'error',
				'msg' => 'No tienes permiso para crear un usuario con ese rol'
			]);
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
		$user->role_id = $request['role']['id'];
        $user->save();
        return response([
            'status' => 'success',
            'data' => $user
		   ], 200);
		
    }
    public function getById(Request $request){
	   	$validator = Validator::make($request->all(), [
			'id' => 'required|string|exists:users',		
		]);
		if ($validator->fails()) {
	   		return response([
				'status' => 'error',
				'msg' => $validator->messages()->all()[0]
			]);
		}
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
			$user = User::find($currentUser['id']);
			$user->role;
	    	return response([
	            'status' => 'error',
				'data' => $user
				
	    	]);
		}
		$user = User::find($request->id);
		$user->role;
	    return response([
	            'status' => 'success',
	            'data' => $user
	    ]);
	}
	public function getAll(Request $request){
		$validator = Validator::make($request->all(), [
			'search' => 'nullable|string',		
		]);
		if ($validator->fails()) {
	   		return response([
				'status' => 'error',
				'msg' => $validator->messages()->all()[0]
			]);
		}
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
			return response([
				'status' => 'success',
				'data' => [$currentUser]
			]);
		}
		if(array_key_exists('search', $request->all())){
			$keys = explode(" ", $request->search);
            $users = User::query();
            if($currentUser['role']['id'] != '1'){
				if($currentUser['role']['id'] == '2'){
					$users = $users->where('role_id', '!=', '2')->where('role_id', '!=', '1');
				}
			}
			$users = $users->where(function ( $query ) use($keys){
                $columns = ['id', 'firstName', 'lastName', 'email'];
                foreach ($keys as $key) {
                    foreach($columns as $column){
                        $query->orWhere($column, 'LIKE', '%' . $key . '%');
                    }
                }
            })->orWhere("id", "=", $currentUser['id'])->get();	
			
		}else{
			if($currentUser['role']['id'] != '1'){
				if($currentUser['role']['id'] == '2'){
					$users = $users->where('role_id', '!=', '2')->where('role_id', '!=', '1');
				}
			}
			$users = $users->get();
		}
		foreach ($users as $user){
			$user->role;
		}
		return response([
			'status' => 'success',
			'data' => $users
		]);
	}
	public function delete(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required|exists:users'
		]);
		if($validator->fails()){
			return response([
				'status' => 'error',
				'msg' => $validator->messages()->all()[0]
			]);
		}
		$user = User::find($request->id);
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		if($currentUser['role']['id'] == '3'
			||	$currentUser['role']['id'] == '2' && ($request['role']['id'] == '2' || $request['role']['id'] == '1')
			|| $currentUser->id == $user->id ){
			return response([
 				'status' => 'error',
				'msg' => 'No tienes permiso para borrar a este usuario'
			]);
		}
		$user->delete();
		return response([
			'status' => 'success']);
	}
	public function update(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required|string|exists:users',
            'user.email'=> [
				'required',
				Rule::unique('users', 'email')->ignore($request->id),
			],	
            'user.password' => 'nullable|string|min:6|max:10',
			'user.id' => [
				'required',
				Rule::unique('users', 'id')->ignore($request->id),
			],	
			'user.firstName' => 'required|string',
			'user.lastName'  => 'required|string',
			'user.role.id' => 'required|numeric|exists:roles,id',
			'user.phone' => 'required|string',
			'user.cellphone' => 'required|string',
			'user.birthDate' => 'required|date',
			'user.city' => 'required|string',
			'user.status' => 'required|string',
			'user.address' => 'required|string',
			//'country' => 'required|string',
        ]);
        if ($validator->fails()) {
           return response([
	                'status' => 'error',
					'msg' => $validator->messages()->all()[0]
				]);
		}
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		
		if($currentUser['role']['id'] == '3' 
			||	$currentUser['role']['id'] == '2' && $request['user']['role']['id'] == '1'){
			if($currentUser['role']['id'] == '2' && $request['user']['role']['id'] == '2'){
				if($currentUser['id'] != $request['user']['id']){
					return response([
						'status' => 'error',
						'msg' => 'No tienes permiso para crear un usuario con ese rol'
					]);
				}
			}
			return response([
				'status' => 'error',
				'msg' => 'No tienes permiso para crear un usuario con ese rol'
			]);
			
		}
		$user = User::find($request->id);
		$request = $request->user;
		$user->email = $request['email'];
        $user->id = $request['id'];
		$user->city = $request['city']; 
		if(array_key_exists('password', $request )){
			$user->password = bcrypt($request['password']);
		}
		$user->firstName = $request['firstName'];
		$user->lastName  = $request['lastName'];
		$user->phone = $request['phone'];
		$user->cellphone = $request['cellphone'];
		$user->birthDate = $request['birthDate'];
		$user->city = $request['city']; 
		$user->status = $request['status'];
		$user->address = $request['address'];
		$user->role_id = $request['role']['id'];
		try{
			$user->save();
		}catch(\Exception $e){
			return response([
	            'status' => 'error',
	            'msg' => 'Database error'
	    	]);
		}

        return response([
            'status' => 'success'
		   ], 200);
	}
}
