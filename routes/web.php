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


Route::group(['middleware' => 'cors'], function() {
    
    
});

Route::get('/', function () {
    return view('welcome'); 
}); 

//Auth
Route::get("/unauthorized", function(){return response()->json(["status"=>"failure","message"=>"unauthorized"]);})->name("login");
Route::post('/Login', 'Auth\LoginController@login');
Route::post('/Register', 'Auth\RegisterController@register');
Route::get('/VerifyEmail/{emailb64}', 'Auth\RegisterController@verifyEmail');
Route::post('/PasswordReset/Request', 'Auth\ForgotPasswordController@forgot');
Route::post('/PasswordReset/SetNow', 'Auth\ForgotPasswordController@setNow');

//Company APIs
Route::post('/CreateCompany', 'CompanyManager@CreateCompany');
Route::get('/GetCompanies', 'CompanyManager@GetCompanies');
Route::get('/DeleteCompany/{id}', 'CompanyManager@DeleteCompany');
Route::post('/UpdateCompany', 'CompanyManager@UpdateCompany');

//Posts
Route::get('/GetPosts', 'Posting\PostManager@GetPosts');
Route::post('/UploadMedia', 'Posting\PostManager@UploadMedia');
Route::post('/CreatePost', 'Posting\PostManager@CreatePost');
Route::get('/DeletePost/{id}', 'Posting\PostManager@DeletePost');
Route::get('/scheduler', 'Posting\PostManager@scheduler');

//SOCIALS
Route::get('/add_linkedin_account', 'SocialMedia\LinkedinController@addAccount');
Route::get('/linkedin_callback', 'SocialMedia\LinkedinController@saveAccessToken');

Route::get('/add_twitter_account', 'SocialMedia\TwitterController@addAccount');
Route::get('/twitter_callback', 'SocialMedia\TwitterController@saveAccessToken');

Route::get('/add_facebook_account', 'SocialMedia\FacebookController@addAccount');
Route::get('/add_instagram_account', 'SocialMedia\TwitterController@addAccount');
Route::get('/add_pinterest_account', 'SocialMedia\FacebookController@addAccount');

Route::get('/get_remaining_social/{id}', 'CompanyManager@socialMedia');

Route::get('/test', function() {
    return 'yes';
    // return "{{env('APP_FRONTEND_URL')}}/dashboard/accounts/add-social-media-accounts?linkedin=true";
});






