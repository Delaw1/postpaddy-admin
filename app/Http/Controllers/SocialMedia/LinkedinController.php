<?php

namespace App\Http\Controllers\SocialMedia;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use \App\LinkedinAccount;
use \App\User;
use \App\Post;
use \App\Utils;
use Session;
use DB;

class LinkedinController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('auth');
      Auth::loginUsingId(1);
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

        Session::put('social_company_id', $company_id);

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

        $server_output = Utils::curlPostRequest("https://www.linkedin.com/oauth/v2/accessToken", "grant_type=authorization_code&code=".$code."&redirect_uri=$redirectURL&client_id=$clientID&client_secret=$clientSecrete", NULL, []);
        $access_token = $server_output->access_token;

        $company_id = Session::get('social_company_id');
        DB::delete('delete from linkedin_accounts where id = ?',[$company_id]);
        LinkedinAccount::create(["company_id" => $company_id, "linkedin_access_token" => $access_token]);

        // return redirect(env("CLOSE_WINDOW_URL"));
        if(env("APP_ENV")=="development") {
          return redirect(env('APP_FRONTEND_URL_DEV')."/dashboard/accounts/add-social-media-accounts?linkedin=true");
        }
        return redirect(env('APP_FRONTEND_URL')."/dashboard/accounts/add-social-media-accounts?linkedin=true");
    }

    public function postNow($post)
    {
        $text = $post->content;
        $media = $post->media;
        $linkedinAccount = LinkedinAccount::where("company_id", '=', $post->company_id)->first();
        if($linkedinAccount == null){return NULL;}

        $response = Utils::curlGetRequest('https://api.linkedin.com/v2/me', "oauth2_access_token=".$linkedinAccount->linkedin_access_token, []);
        $personID = $response->id;
        
        $uploadedContents = [];

        if(!empty($media) && $media != "[]"){
            foreach($media as $m)
            {
                $id = $this->uploadMedia($personID, $linkedinAccount->linkedin_access_token, $m);
                array_push( $uploadedContents, $id );
            }
        }
        
        $data = $this->buildPost($personID, $text, $uploadedContents);
        
        $body = json_encode($data);
        /*var_dump*/ ( Utils::curlPostRequest('https://api.linkedin.com/v2/ugcPosts', 'oauth2_access_token='.$linkedinAccount->linkedin_access_token, $body, ['Content-Type: application/json']) );
    }

    public function uploadMedia($personID, $linkedin_access_token, $fileID){
        $data = array (
            'registerUploadRequest' => 
            array (
              'recipes' => 
              array (
                 'urn:li:digitalmediaRecipe:feedshare-image',
              ),
              'owner' => 'urn:li:person:'.$personID,
              'serviceRelationships' => 
              array (
                  array (
                  'relationshipType' => 'OWNER',
                  'identifier' => 'urn:li:userGeneratedContent',
                ),
              ),
            ),
        );

        $body = json_encode($data);
        $response = Utils::curlPostRequest('https://api.linkedin.com/v2/assets', 'action=registerUpload&oauth2_access_token='.$linkedin_access_token, $body, ['Content-Type: application/json']);
        $uploadURL = $response->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
        $assetID =$response->value->asset;
        
        Utils::curlPostRequest( $uploadURL, "&oauth2_access_token=$linkedin_access_token", File::get(public_path(Utils::UPLOADS_DIR."/$fileID")), [] );

        return $assetID;
    }

    public function buildPost($personID, $text, $uploadedContents){
        $data = array (
            'author' => "urn:li:person:$personID",
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => 
            array (
              'com.linkedin.ugc.ShareContent' => 
              array (
                'shareCommentary' => 
                array (
                  'text' => $text,
                ),
                'shareMediaCategory' => !empty($uploadedContents) ? 'IMAGE' : 'NONE',
                'media' =>
                $this->buildMediaObjectArray($uploadedContents)
              ),
            ),
            'visibility' => 
            array (
              'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            )
        );

        return $data;
    }

    public function buildMediaObjectArray($uploadedContents){
        $contents = array();
        foreach($uploadedContents as $contentID){
            $data =
                array (
                  'status' => 'READY',
                //   'description' => 
                //   array (
                //     'text' => 'Center stage!',
                //   ),
                  'media' => $contentID,
                //   'title' => 
                //   array (
                //     'text' => 'LinkedIn Talent Connect 2018',
                //   ),
            );

            array_push($contents, $data);
        }

        return $contents;
    }
}
