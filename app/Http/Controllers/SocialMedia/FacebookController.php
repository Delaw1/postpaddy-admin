<?php

namespace App\Http\Controllers\SocialMedia;

use Facebook\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FacebookAccount;
use Session;
use App\Gs;
use App\Company;
use \App\Http\Controllers\UserController;
// require('./vendor/facebook/graph-sdk/src/Facebook/autoload.php');

class FacebookController extends Controller
{


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

    Session::put('social_company_id', $company_id);

    // $clientID = env("FACEBOOK_CLIENT_ID'");
    $clientID = "493415521357024";
    $clientSecret = "54c9846d87b01d7920e880fb1881cb99";

    // $clientID = "1484064975133443";
    // $clientSecret = "b3a2299aca447cb36c3a6b9584c84119";
    session_start();
    $fb = new Facebook([
      'app_id' => $clientID,
      'app_secret' => $clientSecret,
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);

    $helper = $fb->getRedirectLoginHelper();

    if (isset($_GET['state'])) {
      $helper->getPersistentDataHandler()->set('state', $_GET['state']);
    }

    $permissions = ['email', 'pages_manage_posts', 'pages_read_engagement']; // Optional permissions
    $loginUrl = $helper->getLoginUrl('https://postslate.com/api/facebook_callback', $permissions);

    return redirect($loginUrl);
  }

  public function saveAccessToken(Request $request)
  {
    session_start();
    $clientID = "493415521357024";
    $clientSecret = "54c9846d87b01d7920e880fb1881cb99";
    $fb = new Facebook([
      'app_id' => $clientID,
      'app_secret' => $clientSecret,
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);
    $helper = $fb->getRedirectLoginHelper();
    try {
      $accessToken = $helper->getAccessToken("https://postslate.com/api/facebook_callback");
    } catch (Facebook\Exception\ResponseException $e) {
      // When Graph returns an error
      // var_dump($helper->getError());
      // echo 'Graph returned an error: ' . $e->getMessage();
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    } catch (Facebook\Exception\SDKException $e) {
      // When validation fails or other local issues
      // echo 'Facebook SDK returned an error: ' . $e->getMessage();
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    }

    if (!isset($accessToken)) {
      if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        // echo "Error: " . $helper->getError() . "\n";
        // echo "Error Code: " . $helper->getErrorCode() . "\n";
        // echo "Error Reason: " . $helper->getErrorReason() . "\n";
        // echo "Error Description: " . $helper->getErrorDescription() . "\n";
        return response()->json(['status' => 'failure', 'error' => $e->getError()]);
      } else {
        header('HTTP/1.0 400 Bad Request');
        return response()->json(['status' => 'failure', 'error' => 'Bad request']);
        // echo 'Bad request';
      }
    }

    $_SESSION['fb_access_token'] = (string) $accessToken;
    $access_token = (string) $accessToken;

    try {
      // Get the \Facebook\GraphNode\GraphUser object for the current user.
      // If you provided a 'default_access_token', the '{access-token}' is optional.
      $response = $fb->get('/me', $access_token);
    } catch (\Facebook\Exception\FacebookResponseException $e) {
      // When Graph returns an error
      // echo 'Graph returned an error: ' . $e->getMessage();
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    } catch (\Facebook\Exception\FacebookSDKException $e) {
      // When validation fails or other local issues
      // echo 'Facebook SDK returned an error: ' . $e->getMessage();
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    }
    $me = $response->getGraphUser();
    $id = $me['id'];
    return response()->json($me);

    $data = ['facebook_id' => $id];

    $validation = Validator::make($data, [
      'facebook_id' => ['required', 'unique:facebook_accounts']
    ]);

    if ($validation->fails()) {
      if (env("APP_ENV") == "development") {
        return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/accounts/add-social-media-accounts?facebook=existing");
      }
      return redirect(env('APP_FRONTEND_URL') . "/dashboard/accounts/add-social-media-accounts?facebook=existing");
    }

    $company_id = Session::get('social_company_id');
    FacebookAccount::create(["company_id" => $company_id, "oauth_token" => $access_token, "facebook_id" => $id]);
   
    if (env("APP_ENV") == "development") {
      return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/accounts/add-social-media-accounts?facebook=true");
    }
    return redirect(env('APP_FRONTEND_URL') . "/dashboard/accounts/add-social-media-accounts?facebook=true");
  }

  public function postNow()
  {
    $fb = new Facebook([
      'app_id' => env('FACEBOOK_CLIENT_ID'),
      'app_secret' => env('FACEBOOK_CLIENT_SECRET'),
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);
    // return 'yes';

    // $facebookAccount = FacebookAccount::where("company_id", '=', $post->company_id)->first();
    // if ($facebookAccount == null) {
    //     return NULL;
    // }
    // $text = $post->content . "\r\n\n" . $post->hashtag;

    $linkData = [
      'link' => 'http://www.example.com',
      'message' => 'Testing PAI',
    ];

    try {
      // Returns a `Facebook\FacebookResponse` object
      $response = $fb->post('/me/feed', $linkData, 'EAAHAwkDgjOABAMPJwmbZAzzrZA0nK2qqFILZAnUYtHHNk8SkXrU4yIgXtT3ZCPE0KEkYgB18MFsbqAMYOsLIAxYv3POlL2yzOKDlROMNlqhzba1NemKveiH9l8R6eX2dH5GlBdefcbcz8IPelJVFifuJRejEi5OnF83XGCJthnjKGUU1pnBaiCNostg1XfCmY9B7iEvHGgWO3Lp7VSiyym6VDdjQ3hZAFdcRD0Q1fOAZDZD');
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $graphNode = $response->getGraphNode();

    echo 'Posted with id: ' . $graphNode['id'];
  }

  public function remove($company_id) {
    $input["id"] = $company_id;

    $validation = Validator::make($input, [
      'id' => ['required', 'exists:facebook_accounts,company_id']
    ]);

    if ($validation->fails()) {
      $data = json_decode($validation->errors(), true);

      $data = ['status' => 'failure', 'error' => $validation->errors()->first()];

      return response()->json($data);
    }

    $sub = (new UserController())->checkSubcription();
    // Check active subscription
    if (!$sub) {
      return response()->json(['status' => 'failure', 'error' => 'Subcription expired, upgrade your plan']);
    }

    if ($sub->remove_social <= 0) {
      return response()->json(['status' => 'failure', 'error' => "You've exceeded your limit, Upgrade you account"]);
    }

    FacebookAccount::where('company_id', $company_id)->delete();

    $sub->remove_social -= 1;
    $sub->save();

    return response()->json(['status' => 'success', 'msg', 'Facebok account successfully deleted']);
  }
}
