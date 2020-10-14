<?php

namespace App\Http\Controllers\SocialMedia;

use Abraham\TwitterOAuth\Response;
use Facebook\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FacebookAccount;
use Session;
use App\Gs;
use App\Company;
use \App\Http\Controllers\UserController;
use \App\Utils;
use stdClass;
// require('./vendor/facebook/graph-sdk/src/Facebook/autoload.php');

class FacebookController extends Controller
{
  protected $fb;

  public function __construct()
  {
    $clientID = "493415521357024";
    $clientSecret = "54c9846d87b01d7920e880fb1881cb99";
    $this->fb = new Facebook([
      'app_id' => $clientID,
      'app_secret' => $clientSecret,
      'default_graph_version' => 'v8.0',
      //'default_access_token' => '{access-token}', // optional
    ]);
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
      // return response()->json(['response' => $response]);
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
    $name = $me['name'];
    // dd($response);
    // return response()->json($id);

    try {
      $response = $fb->get('/' . $id . '/accounts', $access_token);
      // dd($response);
      $fb_pages = $response->getGraphEdge()->asArray();
      // dd($graphObject);
      // $data = $graphObject["data"];
      // return response()->json($graphObject);
    } catch (\Facebook\Exception\FacebookResponseException $e) {
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    } catch (\Facebook\Exception\FacebookSDKException $e) {
      return response()->json(['status' => 'failure', 'error' => $e->getMessage()]);
      // exit;
    }

    $pages = array(["access_token" => $access_token, "name" => $name, "id" => $id, "category" => "personal"]);
    foreach ($fb_pages as $fb_page) {
      array_push($pages, ["access_token" => $fb_page["access_token"], "name" => $fb_page["name"], "id" => $fb_page["id"], "category" => "pages"]);
    }
    // return response()->json($pages);

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
    FacebookAccount::create(["company_id" => $company_id, "access_token" => $access_token, "facebook_id" => $id, "accounts" => $pages]);

    if (env("APP_ENV") == "development") {
      return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/accounts/add-social-media-accounts?facebook=true");
    }
    return redirect(env('APP_FRONTEND_URL') . "/dashboard/accounts/add-social-media-accounts?facebook=true");
  }

  public function postNow($post)
  {
    $text = $post->content . "\r\n\n" . $post->hashtag;
    $media = $post->media;

    $facebookAccount = FacebookAccount::where("company_id", '=', $post->company_id)->first();
    if ($facebookAccount == null) {
      return NULL;
    }

    $linkData = [
      'message' => $text,
    ];

    foreach ($post['platforms']['facebook'] as $account) {
      if ($account['category'] == 'personal') {

        // try {
        //   // Returns a `Facebook\FacebookResponse` object
        //   $response = $fb->post('/me/feed', $linkData, $facebookAccount->access_token);
        // } catch (Facebook\Exceptions\FacebookResponseException $e) {
        //   echo 'Graph returned an error: ' . $e->getMessage();
        //   exit;
        // } catch (Facebook\Exceptions\FacebookSDKException $e) {
        //   echo 'Facebook SDK returned an error: ' . $e->getMessage();
        //   exit;
        // }
        // $graphNode = $response->getGraphNode();
      } else {
        $photoIdArray = array();
        if (!empty($media) && $media != "[]") {
          // return response()->json($media[0]);
          $url = 'https://postslate.com/api/uploads/'.$media[0].'';
          $photo = (Utils::curlPostRequest('https://graph.facebook.com/' . $account['id'] . '/photos', 'url=' . $url . '&published=false&access_token=' . $account['access_token'], [], ['Content-Type: application/json']));
          return response()->json($photo);
          foreach ($media as $m) {
            // $m = '16026340757325.PNG';
            $url = 'https://postslate.com/api/uploads/16026688691109.jpg';
            // $url = 'https://postslate.com/api/uploads/'.$m;
            // return $url;
            $photo = (Utils::curlPostRequest('https://graph.facebook.com/' . $account['id'] . '/photos', 'url=' . $url . '&published=false&access_token=' . $account['access_token'], [], ['Content-Type: application/json']));
            
          }
          return response()->json($photo);
            // array_push($photoIdArray, (object)['media_fbid' => $photo->id]);
          $linkData['attached_media'] = $photoIdArray;
        }

        try {
          $response = $this->fb->post('/' . $account['id'] . '/feed', $linkData, $account['access_token']);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
        $graphNode = $response->getGraphNode();
        $page_id = $graphNode['id'];
        // return $page_id;

      }
    }

    // echo 'Posted with id: ' . $graphNode['id'];
  }

  public function remove($company_id)
  {
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
