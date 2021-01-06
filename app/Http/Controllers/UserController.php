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
use App\Company;
use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient; 

class UserController extends Controller
{
    public function __construct()
    {
    }

    public function welcome() {
        return view('welcome');
    }

    public function EditProfile(Request $request)
    {
        // return response()->json($request->profile_img);
        $input = $request->all();

        // $validation = Validator::make($input, [
        //     'first_name' => ['required', 'string', 'max:255'],
        //     'last_name' => ['required', 'string', 'max:255'],

        // ]);

        // if ($validation->fails()) {
        //     $data = json_decode($validation->errors(), true);

        //     $data = ['status' => 'failure']  + $data;

        //     return response()->json(['status' => 'failure', 'error' => $validation->errors()->first()], 400);
        // }



        if ($request->hasFile('profile_img')) {
            $name = time() . mt_rand(1, 9999) . '.' . $request->file('profile_img')->getClientOriginalExtension();
            $destinationPath = public_path(Utils::PROFILE_IMG_DIR);
            $request->file('profile_img')->move($destinationPath, $name);
            $input['image'] = $name;
        }

        if ($request->profile_img == null || $request->profile_img == 'null') {
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

    public function checkRemoveSocial($company_id, $sub)
    {
        $company = Company::where('id', $company_id)->first();
        // // return $company->removed['linkedin'];
        if ($company->remove_social >= $sub->remove_social) {
            return response()->json(['status' => 'failure', "error" => "You cant remove a social account more than " . $sub->remove_social . " times on this plan"]);
        }

        $company["remove_social"] += 1;
        $company->save();
        return response()->json(['status' => 'success']);
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

    public function checkPostStatus($sub, $client)
    {
        if ($sub->enterprise_id !== null) {
            if ($sub->enterprise->name === "PPD") {
            }
            if ($sub->enterprise->name === "PPC") {
                $company = Company::find($client['company_id']);
                // dd($company);
                // return;
                if ($company->posts === $sub->posts) {
                    return false;
                }
                return true;
            }
            if ($sub->enterprise->name === "TNC") {
                return true;
            }
            return true;
        } else {
            if ($sub->posts <= 0) {
                return false;
            }
            return true;
        }
        // return false;
    }

    public function reducePost($sub, $client)
    {
        if ($sub->enterprise_id !== null) {
            if ($sub->enterprise->name === "PPD") {
            }
            if ($sub->enterprise->name === "PPC") {
                $company = Company::find($client['company_id']);
                $company->posts += 1;
                $company->save();
            }
            if ($sub->enterprise->name === "TNC") {
            }
        } else {
            $sub->posts -= 1;
            $sub->save();
            return $sub->posts;
        }
        return false;
    }

    public function guest()
    {
        return response()->json(["status" => "failure", "message" => "unauthorized"]);
    }

    public function test()
    {
        $gs = Plan::create([
            'name' => 'Freemium',
            'clients' => 5,
            'posts' => 20,
            'accounts' => 3,
            'days' => 14,
            'price' => 0,
            'remove_social' => 2
        ]);
        Plan::create([
            'name' => 'Starter',
            'clients' => 2,
            'posts' => 100,
            'accounts' => 2,
            'days' => 30,
            'price' => 2999,
            'remove_social' => 2
        ]);
        Plan::create([
            'name' => 'Basic',
            'clients' => 6,
            'posts' => 240,
            'accounts' => 5,
            'days' => 14,
            'price' => 6999,
            'remove_social' => 4
        ]);
        Plan::create([
            'name' => 'Plus',
            'clients' => 3,
            'posts' => 150,
            'accounts' => 6,
            'days' => 30,
            'price' => 3999,
            'remove_social' => 5
        ]);
        Plan::create([
            'name' => 'Enterprise',
            'clients' => 20,
            'posts' => 100,
            'accounts' => 7,
            'days' => 30,
            'price' => 5000,
            'remove_social' => 6
        ]);

        // Enterprise::create([
        //     'name' => 'PPD',
        // ]);
        // Enterprise::create([
        //     'name' => 'PPC',
        // ]);
        // Enterprise::create([
        //     'name' => 'TNC',
        // ]);

        $sub = Subscription::where('user_id', 1)->latest()->first();
        $post = $this->checkPostStatus($sub, ['company_id' => 1]);
        if (!$post) {
            return response()->json(['status' => 'failure', 'error' => 'Minimum number of allowed post exceeded, Upgrade you account']);
        }
        return response()->json($post);
    }

    public function test2(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client();
        
            $response = $http->request('POST', 'https://www.postpaddy.com/api/oauth/token', [
                
            ]);
        
        
        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, 200);
    }
}
