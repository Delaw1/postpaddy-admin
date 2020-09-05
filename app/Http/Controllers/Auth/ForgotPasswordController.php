<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use \App\User;
use Illuminate\Support\Facades\Hash;
use \Mailjet\Resources;

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

        $mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'),true,['version' => 'v3.1']);
        
        $token = base64_encode($input["email"]);
        $email = $input["email"];
        // $url = "https://digifigs.com/postslate-emails/password-reset-mail.php?token=".urlencode($token)."&email=".urlencode($email);
    
        // $response = file_get_contents($url);

        $html = file_get_contents(resource_path('views/emails/passwordreset.blade.php'));
        $html = str_replace(
            ['{{TOKEN}}'], 
            [$token],
            $html
        ); 
        $body = [
            'Messages' => [
              [
                'From' => [
                  'Email' => "info@digifigs.com",
                  'Name' => "Postlate"
                ],
                'To' => [
                  [
                    'Email' => $email,
                  ]
                ],
                'Subject' => "Postlate Password Reset",
                'TextPart' => "Postlate Password Reset",
                'HTMLPart' => $html,
                'CustomID' => "AppGettingStartedTest"
              ]
            ]
          ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
        // return var_dump($response->getData());
        // return 'yes'

        return response()->json( ['success'  => 'Password reset email sent'] );
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
