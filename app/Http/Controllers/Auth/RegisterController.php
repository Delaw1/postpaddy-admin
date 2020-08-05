<?php

namespace App\Http\Controllers\Auth;

// require '../../../../vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\MyMail;

use \Mailjet\Resources;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function register(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]); 

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data, 422);
        }

        $user = $this->create($input);

        return response()->json(['status' => 'success', 'user'=>$user], 200);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // $url = "https://digifigs.com/postslate-emails/mail-em.php?name=".urlencode($data["name"])."&email=".urlencode($data["email"]);
    
        // $response = file_get_contents($url);

        $mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'),true,['version' => 'v3.1']);
        
        $html = file_get_contents(resource_path('views/emails/welcomemail.blade.php'));
        $html = str_replace(
            ['{{NAME}}', '{{VERIFY_LINK}}'],
            [$data['name'], "https://postslate.com/api/VerifyEmail/".base64_encode($data['email'])],
            $html
        );
        // return $html2;
        $body = [
            'Messages' => [
              [
                'From' => [
                  'Email' => "info@digifigs.com",
                  'Name' => "Postlate"
                ],
                'To' => [
                  [
                    'Email' => $data['email'],
                    'Name' => $data['name']
                  ]
                ],
                'Subject' => "Welcome to Postslate",
                'TextPart' => "Welcome to Postslate",
                'HTMLPart' => $html,
                'CustomID' => "AppGettingStartedTest"
              ]
            ]
          ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // $response->success() && var_dump($response->getData());
        // return var_dump($response->getData());
        // return 'yes';
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function verifyEmail($emailb64)
    {
        $email = base64_decode($emailb64);
        $user = User::where('email', '=', $email)->first();
        if(!$user){
            die("Invalid verification linkl!");
        }
        else{
            $user->email_verified_at = Carbon::now();
            $user->save();
            return redirect( env('APP_FRONTEND_URL') . '/verify-account-success' );
        }
    }
}