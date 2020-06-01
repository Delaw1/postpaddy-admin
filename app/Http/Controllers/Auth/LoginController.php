<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $response;
        $conditions = array(
            'email' => $request->input('email'),
            'password' => $request->input('password')
        );
        /* check if user credentials is okay */
        if (Auth::attempt($conditions)) {
            $response['success'] = 'Successfully login';
        } else {
            $response['failure'] = 'Incorrect user credentials';
        }
        return response()->json([$response]);
    }
}
