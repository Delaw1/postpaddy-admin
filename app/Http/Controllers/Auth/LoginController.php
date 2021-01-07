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
use Illuminate\Foundation\Application;

class LoginController extends Controller
{
    public function __construct(Application $app)
    {
        $this->app = $app;
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

                $oClient = OClient::where('password_client', 1)->latest()->first();

                $body = [
                    'grant_type' => 'password',
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'username' => $request->input('email'),
                    'password' => $request->input('password'),
                    'scope' => '*'
                ];

                $request = Request::create('/oauth/token', 'POST', $body);
                $result = $this->app->handle($request);

                $result = json_decode($result->getContent(), true);

                $response['token'] = $result['access_token'];
                $response['refresh_token'] = $result['refresh_token'];

                return response()->json([$response]);
            }
        } else {
            $response['failure'] = 'Incorrect email or password';
        }
        return response()->json($response);
    }

    public function refreshToken(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->latest()->first();

        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => '*'
        ];

        $request = Request::create('/oauth/token', 'POST', $body);
        $result = $this->app->handle($request);

        $result = json_decode($result->getContent(), true);

        return response()->json($result);
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
