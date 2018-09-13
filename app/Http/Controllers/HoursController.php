<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Hour;
use JWTAuth;
use Validator;
class HoursController extends Controller
{

    //Crea una hora extra nuevo a partir de los datos recibidos.
    //Solo regresa 'error' con un mensaje 'msg' o solamente 'success' dependiento del resultado de la operación.
    //Todos los parámetros son requeridos.

  public function create(Request $request){
      
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|string:exist:users,id',	
        'initialDate' => 'required|date_format:d-m-Y H:i',
        'finalDate' => 'required|date_format:d-m-Y H:i',
        'description' => 'required|string',
        'status' => 'required|string',
       
    ]);
    if ($validator->fails()) {
       return response([
                'status' => 'error',
                'msg' => $validator->messages()->all()[0]
            ]);
    }
    $currentUser = JWTAuth::toUser($request->token);
      $currentUser->role;
      if($currentUser['role']['id'] == '3' && $request->user_id != $currentUser['id']){
        return response([
            'status' => 'error',
            'msg' => 'No puedes agregar una hora extra de otra persona'
        ]);
      }
    $hour = new Hour;
    $hour->user_id = $request->user_id;
    $hour->finalDate = date('Y-m-d H:i:s',strtotime($request->finalDate));
    $hour->initialDate = date('Y-m-d H:i:s',strtotime($request->initialDate));
    $hour->description = $request->description;
    $hour->status = $request->status;
    if($hour->save())
        return response([
            'status' => 'success'
        ]);
    else
        return response([
           'status' => 'error',
           'msg' => 'Database error'
        ]);
  }

    //Actualiza una hora extra nuevo a partir de los datos recibidos.
    //Solo regresa 'error' con un mensaje 'msg' o solamente 'success' dependiento del resultado de la operación.
    //Todos los parámetros son requeridos.
  public function update(Request $request){
    $currentUser = JWTAuth::toUser($request->token);
    $currentUser->role;
    if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
      return response([
          'status' => 'error',
          'msg' => 'No tienes permiso para realizar esta operación'
      ]);
    }
    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:hours',
        'user_id' => 'required|string:exist:users,id',	
        'initialDate' => 'required|date_format:d-m-Y H:i|before:finalDate',
        'finalDate' => 'required|date_format:d-m-Y H:i',
        'description' => 'required|string',
        'status' => 'required|string', 
    ]);
    if ($validator->fails()) {
       return response([
                'status' => 'error',
                'msg' => $validator->messages()->all()[0]
            ]);
    }

    $hour = Hour::find($request->id);
    if($hour == null){
        return response([
           'status' => 'error',
            'msg' => 'Not found'
        ]);
    }
    if(User::find($request->user_id) == null)
        return response([
            'status' => 'error',
            'msg' =>'User not found'
        ]);
    $hour->user_id = $request->user_id;
    $hour->finalDate = date('Y-m-d H:i:s',strtotime($request->finalDate));
    $hour->initialDate = date('Y-m-d H:i:s',strtotime($request->initialDate));
    $hour->description = $request->description;
    $hour->status = $request->status;
    if($hour->save())
        return response([
            'status' => 'success'
        ]);
    else
        return response([
           'status' => 'error',
           'msg' => 'Database error'
        ]);
  }
    //Devuelve una hora extra nuevo a partir del id recibido.
    //Solo regresa 'error' con un mensaje 'msg' o  'success' y la hora extra dependiento del resultado de la operación.
    //Todos los parámetros son requeridos.
    public function get(Request $request){
        //Este controlador puede ser llamado tanto con el id de la hora como el id del usuario.
        //Si es llamado con el id de la hora regresa únicamente la hora correspondiente
        //Si es llamado con el id del usuario regresa todas las horas de dicho usuario

        
        //Este primer validador es para verificar que se envió el id de la hora.
        //Si es correcto busca la hora extra y la devuelve
        $validator = Validator::make($request->all(), [ 'id' => 'required|string']);
       /* if ($validator->fails()) {
            //Si el primer validador falla chequea si en realidad lo que se envió fue el id del usuario.
            $validator = Validator::make($request->all(), ['user_id' => 'required|string' ]);
            if ($validator->fails()) {
                //Si los dos fallan entonces devuelve un error
                return response([
                    'status' => 'error',
                    'msg' => 'Invalid Fields.'
                ]);
            }else{
                //Por usuario
                $currentUser = JWTAuth::toUser($request->token);
                $currentUser->role;
                if($currentUser['role']['id'] == '3'){
                    $user = $currentUser;
                }else{
                    $user = User::find($request->user_id);
                }
                if($user == null)
                    return response([
                        'status' => 'error',
                        'msg' => 'Not found'
                    ]);
                $hours = $user->hour;
                    return response([
                        'status' => 'success',
                        'data' => $hours
                    ]);
            }
        }
        else{*/
            //Por id
                $hour = Hour::find($request->id);
                if($hour == null){
                    return response([
                    'status' => 'error',
                        'msg' => 'Not found'
                    ]);
                }else{
                    $hour->finalDate = date('d-m-Y H:i',strtotime($hour->finalDate));
                    $hour->initialDate = date('d-m-Y H:i',strtotime($hour->initialDate));
                    $currentUser = JWTAuth::toUser($request->token);
                    $currentUser->role;
                    if($currentUser['role']['id'] == '1' || $currentUser['role']['id'] == '2'){
                        return response([
                            'status' => 'success',
                            'data' => $hour
                        ]);
                    }else{
                        if($hour['user_id'] != $currentUser['id']){
                            return response([
                                'status' => 'error',
                                'msg' => 'No tienes permiso para realizar esta operación'
                            ]);
                        }else{
                            return response([
                                'status' => 'success',
                                'data' => $hour
                            ]);
                        }
                    }
                   
                }
            
         //   }
    }
    //Borra una hora extra nuevo a partir del id recibido.
    //Solo regresa 'error' con un mensaje 'msg' o  solamente 'success'  dependiento del resultado de la operación.
    //Todos los parámetros son requeridos.
    public function delete(Request $request){
        $currentUser = JWTAuth::toUser($request->token);
        $currentUser->role;
        if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
            return response([
                'status' => 'error',
                'msg' => 'No tienes permiso para realizar esta operación'
            ]);
          }
        //Validar parametros
        $validator = Validator::make($request->all(), ['id' => 'required']);
            if ($validator->fails()) {
            return response([
                        'status' => 'error',
                        'msg' => 'Invalid Fields.'
                    ]);
            }
        $hour = Hour::find($request->id);
        if($hour == null)
            return response([
                'status' => 'error',
                'msg' => 'Not found'
            ]);
        if($hour->delete()){
            return response([
                'status' => 'success'
            ]);
        }else{
            return response([
                'status' => 'error',
                'msg' => 'Database Error'
            ]);
        }

    }
    //Devuelve todas las horas extra de la base de datos.
    //Devuelve un arreglo con todas las horas extra encontradas.
    //Todos los parámetros son requeridos.
    public function getAll(Request $request){
        //Limitar la cantidad a mostrar
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
		if(array_key_exists('search', $request->all()) && $request['search'] != ''){
            $keys = explode(" ", $request->search);
            $hours = Hour::query();
            if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2')
                $hours = $hours->where('user_id', '=', $currentUser['id']);
            $hours = $hours->where(function ( $query ) use($keys){
                $columns = ['id', 'user_id', 'initialDate', 'finalDate', 'status'];
                foreach ($keys as $key) {
                    foreach($columns as $column){
                        $query->orWhere($column, 'LIKE', '%' . $key . '%');
                    }
                }
            })->get();
		}else{
            if($currentUser['role']['id'] != '1' && $currentUser['role']['id'] != '2'){
                $hours = Hour::query()->where('user_id', '=', $currentUser['id'])->get();
            }else{
                $hours = Hour::get();
            }
        }
        
        return response([
            'status' => 'success',
            'data' => $hours
        ]);

    }


}
