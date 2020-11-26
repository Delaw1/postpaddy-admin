<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use \App\User;
use Illuminate\Support\Facades\Hash;
use \Mailjet\Resources;
use \App\Http\Controllers\EmailController;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function forgot(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [ 
            'email' => ['required', 'email', 'exists:users']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $user = User::where('email', $input["email"])->first();

        $emailController = new EmailController();

        $emailController->sendPasswordResetEmail([
            'email' => $input["email"],
            'name' => $user->last_name.' '.$user->first_name
        ]);

        return response()->json(['success'  => 'Password reset email sent']);
    }
 
    public function setNow(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'token' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }
        
        $email = base64_decode($input["token"]);
        $user = User::where("email", "=", $email)->first();

        if(!$user)
        {
            return response()->json( ['failure'  => 'Invalid password reset request!'] );
        }

        $user->password = Hash::make($input['new_password']);
        $user->save();

        return response()->json( ['success'  => 'Password reset successfully'] );
    }
}
