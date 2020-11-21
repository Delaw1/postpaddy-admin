<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    public function __construct()
  {
      
  }


    public function ChangePassword(Request $request) { 
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed'
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()->first()], 400);
        }
        // return (Hash::check($request->current_password, Auth::User()->password));
        
        if (Hash::check($request->current_password, Auth::User()->password)) {
            if (strcmp($request->current_password, $request->new_password) == 0) {
                return response()->json(['error' => 'New password cant be the same as current passowrd'], 400);
            }
            $user = Auth::user();
            $user->password = bcrypt($request->new_password);
            $user->save();
            return response()->json(['success' => 'Password changed successfully']);
        } else {
            return response()->json(['error' => 'Your current password is incorrect'], 400);
        }
        
        
       
    }
}
