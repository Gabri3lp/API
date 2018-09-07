<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
class RolesController extends Controller
{
    /*
    public function get(Request $request){
        $validator = Validator::make($request->all(), [ 'id' => 'required|string']);
        if($validator->fails()){
            return response([
                'status' => 'error',
                'msg' => 'Invalid Fields.'
            ]);
        }
        $role = Role::Find($request->id);
        return response([
            'status' => 'success',
            'data' => $role
        ]);
    }*/
    public function getAll(){
        $roles = Role::all();
        return response([
            'status' => 'success',
            'data' => $roles
        ]);
    }
}
