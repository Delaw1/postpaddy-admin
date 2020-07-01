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

//Random
Route::get('/', function () {
    return view('welcome');
});

//Auth
Route::get("/unauthorized", function(){return response()->json(["status"=>"failure","message"=>"unauthorized"]);})->name("login");
Route::post('/Login', 'Auth\LoginController@login');
Route::post('/Register', 'Auth\RegisterController@register');
Route::get('/VerifyEmail/{emailb64}', 'Auth\RegisterController@verifyEmail');
Route::post('/PasswordReset/Request', 'Auth\ForgotPasswordController@forgot');

//Company APIs
Route::post('/CreateCompany', 'CompanyManager@CreateCompany');
Route::get('/GetCompanies', 'CompanyManager@GetCompanies');

//Posts
Route::get('/GetPosts', 'Posting\PostManager@GetPosts');
Route::post('/UploadMedia', 'Posting\PostManager@UploadMedia');
Route::post('/CreatePost', 'Posting\PostManager@CreatePost');

//Social
Route::get('/add_linkedin_account', 'SocialMedia\LinkedinController@addAccount');
Route::get('/linkedin_callback', 'SocialMedia\LinkedinController@saveAccessToken');
Route::get('/postnow', 'HomeController@postNow');