<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use \App\Utils;
use App\Plan;
use Carbon\Carbon;
use App\Subscription;
use App\Notification;
use \Mailjet\Resources;

class UserController extends Controller
{
    public function __construct()
    {
        // Auth::loginUsingId(20);
        // $this->middleware('auth');
    }

    public function EditProfile(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],

        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json(['status' => 'failure', 'error' => $validation->errors()->first()], 400);
        }



        if ($request->hasFile('profile_img')) {
            $name = time() . mt_rand(1, 9999) . '.' . $request->file('profile_img')->getClientOriginalExtension();
            $destinationPath = public_path(Utils::PROFILE_IMG_DIR);
            $request->file('profile_img')->move($destinationPath, $name);
            $input['image'] = $name;
        }

        if ($request->has('profile_img') && $request->profile_img == null) {
            $input['image'] = null;
        }

        $user = User::find(Auth::User()->id)->update($input);

        if ($user) {
            return response()->json(['status' => 'success', 'msg' => 'Profile successfully updated']);
        }
        return response()->json(['status' => 'failure', 'error' => 'Network Error']);
    }

    public function GetUser()
    {
        return response()->json(Auth::User());
    }

    public function changeNotification()
    {
        $user = User::where('id', Auth::User()->id)->update([
            'notification' => !Auth::User()->notification
        ]);
        if ($user) {
            return response()->json(['message' => 'Notification settings successfully changed']);
        }
    }

    // Commercialization
    public function checkSubcription()
    {
        $sub = Subscription::where('user_id', Auth::user()->id)->latest()->first();
        if ($sub) {
            $now = strtotime(Carbon::now());
            $end = strtotime($sub->ended_at);
            if ($end >= $now) {
                return $sub;
            }
        }
        return false;
    }

    public function prevSubcription()
    {
        $sub = Subscription::where('user_id', Auth::user()->id)->get();
        return response()->json(['status' => 'success', 'sub' => $sub]);
    }

    public function currentSubcription()
    {
        $sub = Subscription::where('user_id', Auth::user()->id)->latest()->first();
        $now = strtotime(Carbon::now());
        $end = strtotime($sub->ended_at);
        if ($end >= $now) {
            return response()->json(['status' => 'success', 'sub' => $sub]);
        }
        return response()->json(['status' => 'failure', 'message' => 'No active subscription']);
    }

    public function sendSubscriptionEmail(array $data)
    {
        $mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'), true, ['version' => 'v3.1']);

        $html = file_get_contents(resource_path('views/emails/subscription.blade.php'));
        $html = str_replace(
            ['{{NAME}}', '{{PLAN}}'],
            [$data['name'], $data['plan_name']],
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
                            'Name' => $data['name']
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
    }

    public function getLatestNotifications()
    {
        $notification = Notification::where('user_id', Auth::User()->id)->where('read', 0)->first();
        if ($notification) {
            $notification->read = 1;
            $notification->save();
            // return response()->json(['status' => 'success', 'notification' => $notification]);
        }
        return response()->json(['status' => 'success', 'notification' => $notification]);
    }

    public function getNotifications()
    {
        $notifications = Notification::where('user_id', Auth::User()->id)->get();
        return response()->json(['status' => 'success', 'notification' => $notifications]);
    }

    public function guest()
    {
        return response()->json(["status" => "failure", "message" => "unauthorized"]);
    }
}
