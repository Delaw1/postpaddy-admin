<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * //module->interface == 355
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function addLinkedinAccount(Request $request)
    {
        $request->session()->put('post', $request->input("post"));
        return redirect("https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=77i3oxfcqltbv7&redirect_uri=http://3.23.161.239/signin-linkedin&state=987654321&scope=w_member_social");
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

    public function postToLinkedin(Request $request)
    {
        $post = $request->session()->get('post');
        
        $code = $_GET['code'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.linkedin.com/oauth/v2/accessToken");
        curl_setopt($ch, CURLOPT_POST, 0);   
        curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=authorization_code&code=".$code."&redirect_uri="."http://3.23.161.239/signin-linkedin"."&client_id="."77i3oxfcqltbv7"."&client_secret="."rvoOaE3TfnSu1B9I");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = json_decode( curl_exec ($ch) );
        var_dump($server_output);
        die();
        curl_close ($ch);

       $endpoint = "https://api.linkedin.com/v1/people/~/shares?oauth2_access_token=".$server_output->access_token."&format=json";
        
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
       //return redirect("/dashboard/createpost");
    }
}
