<?php

namespace App\Http\Controllers\SocialMedia;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\TwitterAccount;

class TwitterController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function addAccount(Request $request)
    {
        $input = $request->all();
        $company_id = $request->input("company_id");
        $input["id"] = $company_id;

        $validation = Validator::make($input, [
            'id' => ['required', 'exists:companies']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $request->session()->put('social_company_id', $company_id);

        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        $response = $connection->oauth("oauth/request_token", ["oauth_callback" => env("APP_CALLBACK_BASE_URL")."/twitter_callback?h=23"]);
        
        $oauth_token = $response["oauth_token"];
        $oauth_token_secret = $response["oauth_token_secret"];
        
        $url = $connection->url("oauth/authorize", ["oauth_token" => $oauth_token]);
        
        return redirect($url);
    }

    public function saveAccessToken(Request $request)
    {
        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        $oauth_token = $request->input("oauth_token");
        $oauth_verifier = $request->input("oauth_verifier");
        $response = $connection->oauth("oauth/access_token", ["oauth_token" => $oauth_token, "oauth_verifier" => $oauth_verifier]);

        $oauth_token = $response["oauth_token"];
        $oauth_token_secret = $response["oauth_token_secret"];
        
        $company_id = $request->session()->get('social_company_id');
        TwitterAccount::create(["company_id" => $company_id, "oauth_token" => $oauth_token, "oauth_token_secret" => $oauth_token_secret]);

        return redirect(env("APP_URL")."/closeWindow.html");
    }

    public function postNow(Request $request){
        $post = $request->input("post");
        $token = "AQUT8TrEkDJyJT89uXCExDXQ-s1rS-v-w4khChQ36zSKYRS-vG4Zdp2nHqpCtq8nHKOLSqXAJvWTEqaWF8Deqno4BMg-hSpmN2O4hwDj9NE9Y-AAdMXQHuSUeS_tcByUj1eyc0M9lNTdpg-X2B-cC_w6dPaEg0pLq9HeKA0nQlFtpkBuzEoQBdl2YX3ec3NTJKgV7WZxvz-cWSBmcK5PA67ze22AXCpeULoaBlmjYO5TV1P1KnTWOlJvPnZcBs-5ipzDIgFIAKFB41YqTG1mAY45JGn60RTAAxQM-DWaMwDxkzNyD-fv_sOfj5rQH19MMHA3j_pbVM22RESbYRa_9nE6BBBm8Q";
        $endpoint = "https://api.linkedin.com/v2/people/~/shares?oauth2_access_token=".$token."&format=json";
            
        $data_string = ' 
        {
            "comment": "'.$post.'",
            "visibility": {
                "code": "anyone"
            }
            }
        ';
        
        $ch = curl_init($endpoint);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'x-li-format: json',
            'Content-Length: ' . strlen($data_string),
        ));
        
        $result = curl_exec($ch);
        
        //closing
        curl_close($ch);
        
        var_dump($result);
        die();
    }

}
