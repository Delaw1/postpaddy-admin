<?php

namespace App\Http\Controllers\SocialMedia;

use Facebook\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FacebookAccount;
use Session;

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
    // $redirectURL = env("APP_CALLBACK_BASE_URL") . "/linkedin_callback";

    $fb = new Facebook([
      'app_id' => $clientID,
      'app_secret' => $clientSecret,
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email']; // Optional permissions
    $loginUrl = $helper->getLoginUrl('https://postslate.com/api/facebook_callback', $permissions);

    return redirect($loginUrl);
    // echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    // return $clientID;
  }

  public function saveAccessToken(Request $request)
  {
    $clientID = "493415521357024";
    $clientSecret = "54c9846d87b01d7920e880fb1881cb99";

    $fb = new Facebook([
      'app_id' => $clientID,
      'app_secret' => $clientSecret,
      'default_graph_version' => 'v2.10',
      ]);
    
    $helper = $fb->getRedirectLoginHelper();
    
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exception\ResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exception\SDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    
    if (! isset($accessToken)) {
      if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
      } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
      }
      exit;
    }
    
    // Logged in
    echo '<h3>Access Token</h3>';
    var_dump($accessToken->getValue());
    
    // The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $fb->getOAuth2Client();
    
    // Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    echo '<h3>Metadata</h3>';
    var_dump($tokenMetadata);
    
    // Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId($config['app_id']);
    // If you know the user ID this access token belongs to, you can validate it here
    //$tokenMetadata->validateUserId('123');
    $tokenMetadata->validateExpiration();
    
    if (! $accessToken->isLongLived()) {
      // Exchanges a short-lived access token for a long-lived one
      try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
      } catch (Facebook\Exception\SDKException $e) {
        echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
        exit;
      }
    
      echo '<h3>Long-lived</h3>';
      var_dump($accessToken->getValue());
    }
    
    $_SESSION['fb_access_token'] = (string) $accessToken;
    
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
}
