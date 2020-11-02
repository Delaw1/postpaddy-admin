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

// Route::get('/', function () {
//     return view('welcome');
// });

//Auth
// Route::get("/unauthorized", function () {
//     return response()->json(["status" => "failure", "message" => "unauthorized"]);
// })->name("login");
// Route::post('/Login', 'Auth\LoginController@login');
// Route::post('/Register', 'Auth\RegisterController@register');
// Route::get('/VerifyEmail/{emailb64}', 'Auth\RegisterController@verifyEmail');
// Route::post('/PasswordReset/Request', 'Auth\ForgotPasswordController@forgot');
// Route::post('/PasswordReset/SetNow', 'Auth\ForgotPasswordController@setNow');
// Route::get('/logout', 'Auth\LoginController@logout');
// Route::post('/ChangePassword', 'Auth\ResetPasswordController@ChangePassword');
// // Check if a user is logged in
// Route::get('/isLoggedIn', 'Auth\LoginController@isLoggedIn');

// // User
// Route::post('/EditProfile', 'UserController@EditProfile');
// Route::get('/GetUser', 'UserController@GetUser');



// //Posts
// Route::get('/GetPosts', 'Posting\PostManager@GetPosts');
// Route::get('/GetScheduledPosts', 'Posting\PostManager@GetScheduledPosts');
// Route::post('/UploadMedia', 'Posting\PostManager@UploadMedia');
// Route::post('/CreatePost', 'Posting\PostManager@CreatePost');
// Route::get('/DeletePost/{id}', 'Posting\PostManager@DeletePost');
// Route::post('/UpdatePost', 'Posting\PostManager@UpdatePost');
// Route::get('/scheduler', 'Posting\PostManager@scheduler');

// //SOCIALS
// Route::get('/add_linkedin_account', 'SocialMedia\LinkedinController@addAccount');
// Route::get('/linkedin_callback', 'SocialMedia\LinkedinController@saveAccessToken');
// Route::get('/linkedin_selectaccount', 'SocialMedia\LinkedinController@selectAccount');
// Route::post('/linkedin_saveaccount', 'SocialMedia\LinkedinController@saveAccount');
// Route::post('/CreatePostLin', 'SocialMedia\LinkedinController@postNow');

// Route::get('/add_twitter_account', 'SocialMedia\TwitterController@addAccount');
// Route::get('/twitter_callback', 'SocialMedia\TwitterController@saveAccessToken');

// Route::get('/add_facebook_account', 'SocialMedia\FacebookController@addAccount');
// Route::get('/facebook_callback', 'SocialMedia\FacebookController@saveAccessToken');

// Route::get('/add_instagram_account', 'SocialMedia\TwitterController@addAccount');
// Route::get('/add_pinterest_account', 'SocialMedia\FacebookController@addAccount');

// Route::get('/get_remaining_social/{id}', 'CompanyManager@socialMedia');


// // Notification
// Route::get('/changeNotificationSettings', 'UserController@changeNotification');

// // Payment controller
// Route::get('/pay', 'PaymentController@redirectToGateway')->name('pay');
// Route::get('/paynow', 'PaymentController@redirectToPay');
// Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

// // Subscription
// Route::get('/subscriptions', 'UserController@prevSubcription');
// Route::get('/currentSubscription', 'UserController@currentSubcription');

// // Notification
// Route::get('/getAllNotification', 'UserController@getNotifications');
// Route::get('/getLatestNotification', 'UserController@getLatestNotifications');

// // Cron Jobs
// Route::get('/sendSubscriptionReminder', 'CronJobController@subscriptionReminder');

// Route::get('/test', 'SocialMedia\LinkedinController@test');
// Route::get('/test2', 'CronJobController@subscriptionReminder');
// Route::get('/sendmail', 'EmailController@sendMail');


// Route::group(['middleware' => 'auth:api'], function () {
//     //Company APIs
//     Route::post('/CreateCompany', 'CompanyManager@CreateCompany');
//     Route::get('/GetCompanies', 'CompanyManager@GetCompanies');
//     Route::get('/GetCompany/{id}', 'CompanyManager@GetCompany');
//     Route::get('/DeleteCompany/{id}', 'CompanyManager@DeleteCompany');
//     Route::post('/UpdateCompany', 'CompanyManager@UpdateCompany');
//     Route::post('/RemoveSocialMedia', 'CompanyManager@RemoveSocialMedia');
// });
