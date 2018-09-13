<?php

use Illuminate\Http\Request;
use App\Hour;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
*
*Utilizar prefijos
*
*
*/
Route::post('/login', 'AuthController@login');

Route::group(['middleware' => 'jwt.auth'], function(){
  //Users
  Route::post('/signup', 'UsersController@signup');
  Route::post('get/user', 'UsersController@getById');
  Route::post('update/user', 'UsersController@update');
  Route::post('delete/user', 'UsersController@delete');
  Route::post('get/user/all', 'UsersController@getAll');
  //Hours
  Route::post('/create/hour', 'HoursController@create');
  Route::post('/get/hour', 'HoursController@get');
  Route::post('/update/hour', 'HoursController@update');
  Route::post('/delete/hour', 'HoursController@delete');
  Route::post('/get/hour/all', 'HoursController@getAll');
  //Roles
  Route::get('/get/role/all', 'RolesController@getAll');
});
Route::group(['middleware' => 'jwt.auth'], function(){
   Route::post('/logout', 'AuthController@logout');
});
Route::middleware('jwt.refresh')->get('/token/refresh', 'AuthController@refresh');

Route::post('/report/total', 'ReportController@total');
