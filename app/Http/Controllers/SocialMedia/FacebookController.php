<?php

namespace App\Http\Controllers\SocialMedia;

use Facebook\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\FacebookAccount;
// require('./vendor/facebook/graph-sdk/src/Facebook/autoload.php');

class FacebookController extends Controller
{
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
          } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }
          
          $graphNode = $response->getGraphNode();
          
          echo 'Posted with id: ' . $graphNode['id'];
    }
}
