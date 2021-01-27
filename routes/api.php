<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/', 'UserController@welcome'); 

Route::get('/test', function() {
    return response()->json(["status" => "failure", "message" => "test"]);
});

Route::get('/users', 'SuperAdminController@getUsers');
Route::get('/user/{id}', 'SuperAdminController@getUser');
//Auth
Route::post("/unauthorized", 'UserController@guest')->name("unauthorized");

Route::post('/Login', 'Auth\LoginController@login');
Route::post('/refresh_token', 'Auth\LoginController@refreshToken');


Route::post('/Register', 'Auth\RegisterController@register');
Route::get('/VerifyEmail/{emailb64}', 'Auth\RegisterController@verifyEmail');
Route::post('/PasswordReset/Request', 'Auth\ForgotPasswordController@forgot');
Route::post('/PasswordReset/SetNow', 'Auth\ForgotPasswordController@setNow');
Route::get('/logout', 'Auth\LoginController@logout');

// Check if a user is logged in
Route::get('/isLoggedIn', 'Auth\LoginController@isLoggedIn');




Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/create_admin', 'SuperAdminController@createAdmin');
});




