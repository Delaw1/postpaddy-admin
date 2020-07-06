<?php

namespace App\Http\Controllers\SocialMedia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use \App\User;
use \App\LinkedinAccount;

class LinkedinController extends Controller
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

        $clientID = env("LINKEDIN_CLIENT_ID");
        $redirectURL = env("APP_CALLBACK_BASE_URL") . "/linkedin_callback";

        return redirect("https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=$clientID&redirect_uri=$redirectURL&state=987654321&scope=r_liteprofile,w_member_social");
    }

    public function saveAccessToken(Request $request)
    {
        $clientID = env("LINKEDIN_CLIENT_ID");
        $clientSecrete = env("LINKEDIN_CLIENT_SECRETE");
        $redirectURL = env("APP_CALLBACK_BASE_URL") . "/linkedin_callback";

        $code = $request->input('code');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.linkedin.com/oauth/v2/accessToken");
        curl_setopt($ch, CURLOPT_POST, 0);   
        curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=authorization_code&code=".$code."&redirect_uri=$redirectURL&client_id=$clientID&client_secret=$clientSecrete");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = json_decode( curl_exec ($ch) );
        $access_token = $server_output->access_token;
        curl_close ($ch);

        $company_id = $request->session()->get('social_company_id');
        LinkedinAccount::create(["company_id" => $company_id, "linkedin_access_token" => $access_token]);

        return redirect(env("CLOSE_WINDOW_URL"));
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
