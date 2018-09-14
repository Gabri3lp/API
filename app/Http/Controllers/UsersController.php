<?php


namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Validator;
class UsersController extends Controller
{
	//Funcion que regresa el usuario actual que está autenticado
    public function currentUser(){
		$user = auth()->user();
		return response([
			'data' => $user
		]);
	}

	//Funcion que registra nuevos usuarios
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
		//En segmente se comprueba el rol del usuario actual y verifica si tiene permisos.
		//Si no tiene le regresa un mensaje de error.
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
	//Funcion para obtener un usuario por us ID
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
		//Se comprueba si el usuario tiene los permisos necesarios
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
	//Regresa todos los usuarios
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
		//Si no es un admin o un super admin nada mas devuelve su propio usuario.
		$currentUser = JWTAuth::toUser($request->token);
		$currentUser->role;
		if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
			return response([
				'status' => 'success',
				'data' => [$currentUser]
			]);
		}
        //Si se tiene el parametro de búsqueda y no está vacio genera un query de búsqueda
		if(array_key_exists('search', $request->all())){
			//Si se tiene un parametro de búsqueda ejecuta esta parte del códugo
			$keys = explode(" ", $request->search);
            $users = User::query();
            //Si es un admin, devuelve los usuarios que no sean ni Admin ni SuperAdmin.
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
				//En esta parte el query siempre trata de incluir el propio usuario.
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

	//Funcion para borrar un usuario
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
		//Aquí comprueba si el usuario tiene permisos de borrar dicho usuario. Nunca se puede borrar tu propio usuario.
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
	//Funcion de actualizar un usuario
	public function update(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required|string|exists:users',
            'user.email'=> [
				'required',
				Rule::unique('users', 'email')->ignore($request->id), //El email debe ser nuevo entre los usuarios,
			],														  //a menos de que sea el tuyo sin modificar.
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
		//Se verifica si tienes permiso para modificar este usuario
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
		$user->save();

        return response([
            'status' => 'success'
		   ], 200);
	}
}
