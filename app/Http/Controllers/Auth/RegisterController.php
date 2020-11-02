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
use DateTime;
use \Mailjet\Resources;
use App\Plan;
use App\Subscription;
use App\Notification;

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
        // $this->middleware('guest');
    }

    protected function register(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]); 

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
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
            [$data['last_name']." ".$data['first_name'], "https://postslate.com/api/VerifyEmail/".base64_encode($data['email'])],
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
                    'Email' => $data['email'],
                    'Name' => $data['last_name']." ".$data['first_name']
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
        
        $plan = Plan::where('name', 'Freemium')->first();
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plan_id' => $plan->id,
            'started_at' => Carbon::now(),
            'ended_at' => Carbon::now()->addDays($plan->days)
        ]);
        
        Subscription::create([
          'user_id' => $user->id,
          'plan_id' => $plan->id,
          'clients' => $plan->clients,
          'posts' => $plan->posts,
          'accounts' => $plan->accounts,
          'remove_social' => $plan->remove_social,
          'started_at' => $user->started_at,
          'ended_at' => $user->ended_at
        ]);
        Notification::create([
          'user_id' => $user->id,
          'message' => "You've successfully subscribe to " . $plan->name.". Subscription will expire on ".date('d, M. Y', strtotime($user->ended_at))
      ]);

      $html = file_get_contents(resource_path('views/emails/subscription.blade.php'));
            $html = str_replace(
                ['{{NAME}}', '{{PLAN}}'],
                [$user->last_name." ".$user->first_name, $plan->name],
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
                                'Email' => $user->email,
                                'Name' => $user->last_name." ".$user->first_name
                            ]
                        ],
                        'Subject' => "Subscription successfully",
                        'TextPart' => "Subscription successfully",
                        'HTMLPart' => $html,
                        'CustomID' => "AppGettingStartedTest"
                    ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $user;
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