<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;

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
        if (Auth::attempt($conditions)) 
        {
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
                    return response()->json([$response], 200);
                }
        } else {
            $response['failure'] = 'Incorrect email or password';
        }
        return response()->json([$response], 400);
    }
}