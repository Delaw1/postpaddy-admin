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
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\EmailController;

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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $user = User::where(['email' => $request->email])->whereNotNull('password')->first();
        if($user) {
            return response()->json(['status' => 'failure', 'email' => ['Email already exist']]);
        }

        $user = User::where(['email' => $request->email, 'password' => null])->first();
        if($user) {
            // $validation = Validator::make($input, [
            //     'id' => ['required', 'integer']
            // ]);
    
            // if ($validation->fails()) {
            //     $data = json_decode($validation->errors(), true);
    
            //     $data = ['status' => 'failure']  + $data;
    
            //     return response()->json($data);
            // }
    
            $user = $this->completeReg($input);
        } else {
            $user = $this->create($input);
        }
        
        return response()->json(['status' => 'success', 'user' => $user], 200);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
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
            'message' => "You've successfully subscribe to " . $plan->name . ". Subscription will expire on " . date('d, M. Y', strtotime($user->ended_at))
        ]);

        $emailController = new EmailController();

        $emailController->sendVerificationEmail([
            'name' => $user->last_name . " " . $user->first_name,
            'email' => $user->email
        ]);
        
        $emailController->sendSubscriptionEmail([
            'name' => $user->last_name . " " . $user->first_name,
            'plan_name' => $plan->name,
            'email' => $user->email
        ]);

        return $user;
    }

    public function verifyEmail($emailb64)
    {
        $email = base64_decode($emailb64);
        $user = User::where('email', '=', $email)->first();
        if (!$user) {
            die("Invalid verification linkl!");
        } else {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return redirect(env('APP_FRONTEND_URL') . '/verify-account-success');
        }
    }

    public function isSubscribe(Request $request)
    {
        // session(['postslate_id' => 2]);
        $user_id = $request->session()->get('postslate_id');
        if ($user_id) {
            $user = User::find($user_id);
            if($user) {
                if ($user->password === null) {
                    return response()->json($user);
                }
            }
        }
        return response()->json([]);
    }

    public function completeReg(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->password = Hash::make($data['password']);

        $user->save();

        (new EmailController())->sendVerificationEmail([
            'name' => $data['last_name'] . " " . $data['first_name'],
            'email' => $data['email']
        ]);

        return $user;
    }
}
