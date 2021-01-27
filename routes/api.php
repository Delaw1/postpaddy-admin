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
// Check if a user has subscribe before signup
Route::get('/isSubscribe', 'Auth\RegisterController@isSubscribe');

//SOCIALS
Route::get('/add_linkedin_account', 'SocialMedia\LinkedinController@addAccount');
Route::get('/linkedin_callback', 'SocialMedia\LinkedinController@saveAccessToken');

Route::get('/add_twitter_account', 'SocialMedia\TwitterController@addAccount');
Route::get('/twitter_callback', 'SocialMedia\TwitterController@saveAccessToken');

Route::get('/add_facebook_account', 'SocialMedia\FacebookController@addAccount');
Route::get('/facebook_callback', 'SocialMedia\FacebookController@saveAccessToken');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/create_admin', 'SuperAdminController@createAdmin');
    


    Route::post('/ChangePassword', 'Auth\ResetPasswordController@ChangePassword');
    
    //Company APIs
    Route::post('/CreateCompany', 'CompanyManager@CreateCompany');
    Route::get('/GetCompanies', 'CompanyManager@GetCompanies');
    Route::get('/GetCompany/{id}', 'CompanyManager@GetCompany');
    Route::get('/DeleteCompany/{id}', 'CompanyManager@DeleteCompany');
    Route::post('/UpdateCompany', 'CompanyManager@UpdateCompany');
    Route::post('/RemoveSocialMedia', 'CompanyManager@RemoveSocialMedia');

    // User
    Route::post('/EditProfile', 'UserController@EditProfile');
    Route::get('/GetUser', 'UserController@GetUser');

    //Posts
    Route::get('/GetPosts', 'Posting\PostManager@GetPosts');
    Route::get('/GetScheduledPosts', 'Posting\PostManager@GetScheduledPosts');
    Route::post('/UploadMedia', 'Posting\PostManager@UploadMedia');
    Route::post('/CreatePost', 'Posting\PostManager@CreatePost');
    Route::get('/DeletePost/{id}', 'Posting\PostManager@DeletePost');
    Route::post('/UpdatePost', 'Posting\PostManager@UpdatePost');
    Route::get('/scheduler', 'Posting\PostManager@scheduler');

    //SOCIALS
    
    Route::get('/linkedin_selectaccount', 'SocialMedia\LinkedinController@selectAccount');
    Route::post('/linkedin_saveaccount', 'SocialMedia\LinkedinController@saveAccount');
    Route::post('/CreatePostLin', 'SocialMedia\LinkedinController@postNow');

    


    Route::get('/add_instagram_account', 'SocialMedia\TwitterController@addAccount');
    Route::get('/add_pinterest_account', 'SocialMedia\FacebookController@addAccount');

    Route::get('/get_remaining_social/{id}', 'CompanyManager@socialMedia');


    // Notification
    Route::get('/changeNotificationSettings', 'UserController@changeNotification');

    // Payment controller
    
    
    


    // Subscription
    Route::get('/subscriptions', 'UserController@prevSubcription');
    Route::get('/currentSubscription', 'UserController@currentSubcription');

    // Notification
    Route::get('/getAllNotification', 'UserController@getNotifications');
    Route::get('/getLatestNotification', 'UserController@getLatestNotifications');

    
});

// Cron Jobs
Route::get('/sendSubscriptionReminder', 'CronJobController@subscriptionReminder');

Route::get('/paynow', 'PaymentController@redirectToPay');
Route::get('/pay', 'PaymentController@redirectToGateway')->name('pay');
Route::get('/paywithoutsignup', 'PaymentController@paywithoutsignup');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

Route::get('/payforenterprise', 'PaymentController@payforenterprise');



// Route::get('/test2', 'CronJobController@subscriptionReminder');
Route::get('/sendmail', 'EmailController@sendMail');

Route::prefix('admin')->group(function() {
    Route::post('/setEnterprisePackage', 'AdminController@setEnterprisePackage');
});
