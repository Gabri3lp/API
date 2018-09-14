<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
class RolesController extends Controller
{
   //Funcion que regresa todos los roles de la base de datos
    public function getAll(){
        $roles = Role::all();
        return response([
            'status' => 'success',
            'data' => $roles
        ]);
    }
}
