<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use App\Gs;
use App\User;

class LoginController extends Controller
{
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    public function login(Request $request){ 
        $response;
        $conditions = array(
            'email' => $request->input('email'),
            'password' => $request->input('password')
        );
        /* check if user credentials is okay */
        // $gs = Gs::first();
        if (Auth::attempt($conditions)) 
        {
            // $start = strtotime(Auth::user()->created_at);
            // $end = strtotime('now');
            // $diff = floor(abs($end - $start) / 86400);
           
            // if($diff >= $gs->days) {
                
            //     $user = User::where("id", Auth::user()->id)->update([
            //         'status' => 0
            //     ]);
            //     if($user) {
            //         Auth::user()->status = 0;
            //     }
            // }

               if(Auth::user()->email_verified_at == NULL){
                   $response['failure'] = 'Please verify your email';
               }
               elseif(Str::contains($request->header("Content-Type"), 'form')){
                    return redirect($request->headers->get('origin') . "/dashboard" );
                }
                else
                {
                    $response['success'] = 'Successfully logged in';
                    $response["user_data"] = Auth::user();
                    return response()->json([$response]);
                }
        } else {
            $response['failure'] = 'Incorrect email or password';
        }
        return response()->json([$response]);
    }

    public function isLoggedIn() {
        if(Auth::check()) {
            return response()->json(["status" => true, "msg" => "User is logged in"]);
        }
        return response()->json(["status" => false, "msg" => "User not logged in"]);
    }

    public function logout() {
        if(Auth::check()) {
            Auth::logout();
            return response()->json(["msg" => "Logout successful"]);
        }
        return response()->json(["msg" => "User already logged out"]);
    }
}