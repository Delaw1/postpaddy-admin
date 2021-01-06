<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use App\Gs;
use App\User;
use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient; 

class LoginController extends Controller
{
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $response;
        $conditions = array(
            'email' => $request->input('email'),
            'password' => $request->input('password')
        );
        /* check if user credentials is okay */

        if (Auth::attempt($conditions)) {
            if (Auth::user()->email_verified_at == NULL) {
                $response['failure'] = 'Please verify your email';
            } elseif (Str::contains($request->header("Content-Type"), 'form')) {
                return redirect($request->headers->get('origin') . "/dashboard");
            } else {
                $response['success'] = 'Successfully logged in';
                $response["user_data"] = Auth::user();
                // $response['token'] = Auth::user()->createToken('myApp')->accessToken;

                // $oClient = OClient::where('password_client', 1)->first();
                $client = new Client();

                $result = $client->request('POST', 'https://www.postpaddy.com/api/oauth/token', [
                    'form_params' =>
                    [
                        'grant_type' => 'password',
                        'client_id' => 3,
                        'client_secret' => '1LYkAjc8uFUrLOgQwP7mAgApyXLqWdl0jJ6pPkvF',
                        'username' => $request->input('email'),
                        'password' => $request->input('password'),
                        'scope' => '*'
                    ]
        
                ]);

                $result = json_decode((string) $result->getBody(), true);
                
                $response['token'] = $result['access_token'];
                $response['refresh_token'] = $result['refresh_token'];
                
                //     $response = $http->request('POST', 'https://www.postpaddy.com/api/oauth/token', [
                //         'form_params' => [
                //             'grant_type' => 'password',
                //             'client_id' => $oClient->id,
                //             'client_secret' => $oClient->secret,
                //             'username' => $request->input('email'),
                //             'password' => $request->input('password'),
                //             'scope' => '*',
                //         ],
                //     ]);
                
                
                // $result = json_decode((string) $response->getBody(), true);
                // return response()->json($result, 200);

                return response()->json([$response]);
            }
        } else {
            $response['failure'] = 'Incorrect email or password';
        }
        return response()->json($response);
    }

    public function isLoggedIn()
    {
        if (Auth::guard('api')->check()) {
            return response()->json(["status" => true, "msg" => "User is logged in"]);
        }
        return response()->json(["status" => false, "msg" => "User not logged in"]);
    }

    public function logout()
    {
        if (Auth::guard('api')->check()) {
            // Auth::logout();
            Auth::user()->AauthAcessToken()->delete();
            return response()->json(["msg" => "Logout successful"]);
        }
        return response()->json(["msg" => "User already logged out"]);
    }
}
