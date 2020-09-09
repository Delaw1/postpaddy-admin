<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        // Auth::loginUsingId(4);
        $this->middleware('auth');
    }

    public function EditProfile(Request $request) {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'category' => 'required',
            'employees' => 'required|integer',
            'phone' => 'required',
            'business_name' => 'required'
        ]); 

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json(['status' => 'failure', 'error' => $validation->errors()->first()], 400);
        }

        $user = User::where('id', Auth::User()->id)->update($request->all());
        if($user) {
            return response()->json(['status' => 'success', 'msg' => 'Profile successfully updated']);
        }
        return response()->json(['status' => 'failure', 'error' => 'Network Error']);
    }

    public function GetUser() {
        return response()->json(Auth::User());
    }
}
