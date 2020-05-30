<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/Login', 'Auth\LoginController@login');
Route::post('/Register', 'Auth\RegisterController@register');

Route::get('/signin-linkedin', 'HomeController@saveLinkedinToken');
Route::get('/postnow', 'HomeController@postNow');
Route::post('/add-linkedin-account', 'HomeController@addLinkedinAccount');
Route::get('/home', 'HomeController@index')->name('home');
