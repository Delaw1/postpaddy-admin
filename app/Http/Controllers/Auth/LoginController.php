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
        if (Auth::attempt($conditions)) {
                if(Str::contains($request->header("Content-Type"), 'form')){
                    return redirect($request->headers->get('origin') . "/dashboard" );
                }
                else
                {
                    $response['success'] = 'Successfully logged in';
                }
        } else {
            $response['failure'] = 'Incorrect user credentials';
        }
        return response()->json([$response]);
    }
}