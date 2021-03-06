<?php

namespace App\Http\Controllers\SocialMedia;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\TwitterAccount;
use \App\Post;
use \App\Utils;
use Session;
use DB;
use Illuminate\Contracts\Session\Session as SessionSession;
use App\Gs;
use App\Company;
use \App\Http\Controllers\UserController;

class TwitterController extends Controller
{
    public function __construct()
    {
        
    }

    public function addAccount(Request $request)
    {
        $input = $request->all();
        $company_id = $request->input("company_id");
        $input["id"] = $company_id;

        $validation = Validator::make($input, [
            'id' => ['required', 'exists:companies']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        if ($request->has('user_id')) {
            Session::put('user_id', $request->input("user_id"));
        }
        Session::put('social_company_id', $company_id);

        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        $response = $connection->oauth("oauth/request_token", ["oauth_callback" => env("APP_CALLBACK_BASE_URL") . "/twitter_callback?h=23"]);

        $oauth_token = $response["oauth_token"];
        $oauth_token_secret = $response["oauth_token_secret"];

        $url = $connection->url("oauth/authorize", ["oauth_token" => $oauth_token]);

        return redirect($url);
    }

    public function saveAccessToken(Request $request)
    {
        if ($request->session()->has('user_id')) {
            Auth::loginUsingId(Session::get('user_id'));
        }

        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        $oauth_token = $request->input("oauth_token");
        $oauth_verifier = $request->input("oauth_verifier");
        $response = $connection->oauth("oauth/access_token", ["oauth_token" => $oauth_token, "oauth_verifier" => $oauth_verifier]);
        // return response()->json($response);
        
        $oauth_token = $response["oauth_token"];
        $oauth_token_secret = $response["oauth_token_secret"];

        $twitter_id = $response["user_id"];
        $twitter_username = $response["screen_name"];

        $result = $connection->get("users/show", ["id" => $twitter_id]);
        $twitter_name = $result->name;
        $data = ['twitter_id' => $twitter_id];
        // return response()->json($result);

        // $validation = Validator::make($data, [
        //   'twitter_id' => ['required', 'unique:twitter_accounts']
        // ]);

        // if ($validation->fails()) {
        //   if (env("APP_ENV") == "development") {
        //     return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/accounts/add-social-media-accounts?twitter=existing");
        //   }
        //   return redirect(env('APP_FRONTEND_URL') . "/dashboard/accounts/add-social-media-accounts?twitter=existing");
        // }

        $company_id = Session::get('social_company_id');

        // DB::delete('delete from twitter_accounts where id = ?',[$company_id]);
        TwitterAccount::create(["company_id" => $company_id, "oauth_token" => $oauth_token, "oauth_token_secret" => $oauth_token_secret, "twitter_id" => $twitter_id, "name" => $twitter_name, "username" => $twitter_username]);

        // return redirect(env("CLOSE_WINDOW_URL"));

        if (env("APP_ENV") == "development") {
            return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/client-accounts/add-social-media-accounts?twitter=true");
        }
        return redirect(env('APP_FRONTEND_URL') . "/dashboard/client-accounts/add-social-media-accounts?twitter=true");
    }

    public function postNow($post)
    {
        $text = $post->content . "\r\n\n" . $post->hashtag;
        $media = $post->media;
        $twitterAccount = TwitterAccount::where("company_id", '=', $post->company_id)->first();
        if ($twitterAccount == null) {
            return NULL;
        }

        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), $twitterAccount->oauth_token, $twitterAccount->oauth_token_secret);

        $data = array("status" => $text);

        if (!empty($media) && $media != "[]") {
            $data["media_ids"] = '';
            foreach ($media as $m) {
                $ext = strtolower(pathinfo($m, PATHINFO_EXTENSION));
                if($ext == "mp4" || $ext == "3gp" || $ext == "avi" || $ext == "mov") {
                     $upload = $connection->upload('media/upload', ['media' => public_path(Utils::UPLOADS_DIR . "/$m"), 'media_type' => 'video/mp4'], true);
                    $data["media_ids"] .= $upload->media_id . ",";
                } else {
                    $upload = $connection->upload('media/upload', ['media' => public_path(Utils::UPLOADS_DIR . "/$m")]);
                    $data["media_ids"] .= $upload->media_id_string . ",";
                }
               
            }
        }

        $statusUpdate = $connection->post("statuses/update", $data);
    }

    public function remove($company_id)
    {
        $input["id"] = $company_id;

        $validation = Validator::make($input, [
            'id' => ['required', 'exists:twitter_accounts,company_id']
        ]);

        if ($validation->fails()) {
            $data = ['status' => 'failure', 'error' => $validation->errors()->first()];

            return response()->json($data);
        }

        $userController = new UserController();
        $sub = $userController->checkSubcription();
        // Check active subscription
        if (!$sub) {
          return response()->json(['status' => 'failure', 'error' => 'Subcription expired, upgrade your plan']);
        }
    
        $remove = $userController->checkRemoveSocial($company_id, $sub);
        
        if($remove->getData()->status === 'failure') {
            return $remove;
        }

        TwitterAccount::where('company_id', $company_id)->delete();

        return response()->json(['status' => 'success', 'msg' => 'Twitter account successfully deleted']);
    }
}
