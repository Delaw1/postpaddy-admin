<?php

namespace App\Http\Controllers\SocialMedia;

use App\Company;
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
use GuzzleHttp\Client;
use stdClass;
use \App\Gs;
use App\Plan;
use App\Enterprise;
use DateTime;
use Carbon\Carbon;
use App\Subscription;
use \App\Http\Controllers\UserController;

class LinkedinController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth');
    // Auth::loginUsingId(4); 
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

    $clientID = env("LINKEDIN_CLIENT_ID");
    $redirectURL = env("APP_CALLBACK_BASE_URL") . "/linkedin_callback";

    return redirect("https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=$clientID&redirect_uri=$redirectURL&state=987654321&scope=r_liteprofile,w_member_social,w_organization_social,r_organization_social,rw_organization_admin");
  }

  public function saveAccessToken(Request $request)
  {
    if ($request->session()->has('user_id')) {
      Auth::loginUsingId(Session::get('user_id'));
    }

    $clientID = env("LINKEDIN_CLIENT_ID");
    $clientSecrete = env("LINKEDIN_CLIENT_SECRETE");
    $redirectURL = env("APP_CALLBACK_BASE_URL") . "/linkedin_callback";

    $code = $request->input('code');

    $server_output = Utils::curlPostRequest("https://www.linkedin.com/oauth/v2/accessToken", "grant_type=authorization_code&code=" . $code . "&redirect_uri=$redirectURL&client_id=$clientID&client_secret=$clientSecrete", NULL, []);
    $access_token = $server_output->access_token;
    $get_me = Utils::curlGetRequest('https://api.linkedin.com/v2/me', 'oauth2_access_token=' . $access_token, []);
    $linkedin_id = $get_me->id;
    $data = ['linkedin_id' => $linkedin_id];

    // $validation = Validator::make($data, [
    //   'linkedin_id' => ['required', 'unique:linkedin_accounts']
    // ]);

    // if ($validation->fails()) {
    //   if (env("APP_ENV") == "development") {
    //     return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/accounts/add-social-media-accounts?linkedin=existing");
    //   }
    //   return redirect(env('APP_FRONTEND_URL') . "/dashboard/accounts/add-social-media-accounts?linkedin=existing");
    // }


    $company_id = Session::get('social_company_id');

    // DB::delete('delete from linkedin_accounts where id = ?', [$company_id]);
    LinkedinAccount::create(["company_id" => $company_id, "linkedin_access_token" => $access_token, "linkedin_id" => $linkedin_id]);

    if (env("APP_ENV") == "development") {
      return redirect(env('APP_FRONTEND_URL_DEV') . "/dashboard/linkedin_select_account");
    }
    return redirect(env('APP_FRONTEND_URL') . "/dashboard/linkedin_select_account");
  }

  public function selectAccount(Request $request)
  {
    $input = $request->all();
    $input["id"] = $request->input("company_id");

    $validation = Validator::make($input, [
      'id' => ['required', 'exists:companies'],
      'company_id' => ['required', 'exists:linkedin_accounts']
    ]);



    if ($validation->fails()) {
      $data = json_decode($validation->errors(), true);

      $data = ['status' => 'failure']  + $data;

      return response()->json($data);
    }
    $linkedin = LinkedinAccount::where('company_id', $input["id"])->first();
    if (!empty($linkedin->accounts)) {
      return response()->json(['error' => 'Account already selected'], 409);
    }
    $access_token = $linkedin->linkedin_access_token;

    $org_data = [];

    $get_me = Utils::curlGetRequest('https://api.linkedin.com/v2/me', 'oauth2_access_token=' . $access_token, []);
    array_push($org_data, array("name" => $get_me->localizedFirstName . ' ' . $get_me->localizedLastName, "id" => $get_me->id, "category" => "personal"));

    $res = Utils::curlGetRequest('https://api.linkedin.com/v2/organizationAcls', 'q=roleAssignee&role=ADMINISTRATOR&state=APPROVED&oauth2_access_token=' . $access_token . '&projection=(elements*(*,organization~(localizedName)))', []);
    foreach ($res->elements as $elem) {
      $id = explode(":", $elem->organization)[3];
      array_push($org_data, array("name" => $elem->{'organization~'}->localizedName, "id" => $id, "category" => "company"));
    }
    return response()->json($org_data);
  }

  public function saveAccount(Request $request)
  {
    $input = $request->all();
    $input["id"] = $request->input("company_id");

    $validation = Validator::make($input, [
      'id' => ['required', 'exists:companies'],
      'company_id' => ['required', 'exists:linkedin_accounts'],
      'accounts' => ['required']
    ]);

    if ($validation->fails()) {
      $data = json_decode($validation->errors(), true);

      $data = ['status' => 'failure']  + $data;

      return response()->json($data);
    }
    $update = LinkedinAccount::where('company_id', $input["id"])->update([
      'accounts' => $request->accounts
    ]);
    if ($update) {
      return response()->json(['status' => 'success']);
    }
    return response()->json(['status' => 'failure', 'msg' => 'Network error']);
  }

  public function getAccount(Request $request)
  {
    $id = $request->input("company_id");
    $validation = Validator::make($request->all(), [
      'company_id' => ['required', 'exists:linkedin_accounts']
    ]);

    if ($validation->fails()) {
      $data = json_decode($validation->errors(), true);

      $data = ['status' => 'failure']  + $data;

      return response()->json($data, 400);
    }
    $accounts = LinkedinAccount::where('company_id', $id)->first();
    if ($accounts) {
      return response()->json($accounts->accounts);
    }
    return null;
  }

  public function postNow($post)
  {
    $text = $post->content . "\r\n\n" . $post->hashtag;
    $media = $post->media;
    $linkedinAccount = LinkedinAccount::where("company_id", '=', $post->company_id)->first();

    if ($linkedinAccount == null) {
      return NULL;
    }

    foreach ($post['platforms']['linkedin'] as $account) {
      // return $account;
      if ($account['category'] == 'personal') {
        // $response = Utils::curlGetRequest('https://api.linkedin.com/v2/me', "oauth2_access_token=" . $linkedinAccount->linkedin_access_token, []);
        $personID = $account['id'];

        $uploadedContents = [];

        if (!empty($media) && $media != "[]") {
          foreach ($media as $m) {
            $id = $this->uploadMedia($personID, 'person', $linkedinAccount->linkedin_access_token, $m);
            array_push($uploadedContents, $id);
          }
        }

        $data = $this->buildPost($personID, $text, $uploadedContents);

        $body = json_encode($data);
        /*var_dump*/
        (Utils::curlPostRequest('https://api.linkedin.com/v2/ugcPosts', 'oauth2_access_token=' . $linkedinAccount->linkedin_access_token, $body, ['Content-Type: application/json']));
      } else {
        $uploadedContents = [];


        if (!empty($media) && $media != "[]") {
          foreach ($media as $m) {
            $id = $this->uploadMedia($account['id'], 'organization', $linkedinAccount->linkedin_access_token, $m);
            array_push($uploadedContents, $id);
          }
        }
        // return $uploadedContents;
        $data = $this->buildOrgPost($account['id'], $text, $uploadedContents);
        // return $data;
        $body = json_encode($data);
        // return $body;
        (Utils::curlPostRequest('https://api.linkedin.com/v2/shares', 'oauth2_access_token=' . $linkedinAccount->linkedin_access_token, $body, ['Content-Type: application/json']));
      }
    }
  }

  public function uploadMedia($ID, $type, $linkedin_access_token, $fileID)
  {
    $data = array(
      'registerUploadRequest' =>
      array(
        'recipes' =>
        array(
          'urn:li:digitalmediaRecipe:feedshare-image',
        ),
        'owner' => "urn:li:$type:$ID",
        'serviceRelationships' =>
        array(
          array(
            'relationshipType' => 'OWNER',
            'identifier' => 'urn:li:userGeneratedContent',
          ),
        ),
      ),
    );

    $body = json_encode($data);
    $response = Utils::curlPostRequest('https://api.linkedin.com/v2/assets', 'action=registerUpload&oauth2_access_token=' . $linkedin_access_token, $body, ['Content-Type: application/json']);
    $uploadURL = $response->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
    $assetID = $response->value->asset;

    // $res = Utils::curlPutRequest($uploadURL, File::get(public_path(Utils::UPLOADS_DIR . "/$fileID")), ['Authorization: Bearer '.$linkedin_access_token]);

    $client = new Client();
    $res = $client->request('PUT', $uploadURL, [
      'headers' => ['Authorization' => 'Bearer ' . $linkedin_access_token],
      'body' => fopen(public_path(Utils::UPLOADS_DIR . "/$fileID"), 'r'),
      'verify' => true
    ]);

    return $assetID;
  }

  public function buildPost($personID, $text, $uploadedContents)
  {
    if (!empty($uploadedContents)) {
      $data = array(
        'author' => "urn:li:person:$personID",
        'lifecycleState' => 'PUBLISHED',
        'specificContent' =>
        array(
          'com.linkedin.ugc.ShareContent' =>
          array(
            'shareCommentary' =>
            array(
              'text' => $text,
            ),
            'shareMediaCategory' => 'IMAGE',
            'media' => $this->buildMediaObjectArray($uploadedContents)
          ),
        ),
        'visibility' =>
        array(
          'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
        )
      );
    } else {
      $data = array(
        'author' => "urn:li:person:$personID",
        'lifecycleState' => 'PUBLISHED',
        'specificContent' =>
        array(
          'com.linkedin.ugc.ShareContent' =>
          array(
            'shareCommentary' =>
            array(
              'text' => $text,
            ),
            'shareMediaCategory' => 'NONE'
          ),
        ),
        'visibility' =>
        array(
          'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
        )
      );
    }


    return $data;
  }

  public function buildOrgPost($orgID, $text, $uploadedContents)
  {

    if (empty($uploadedContents)) {
      $data = array(
        "distribution" => array(
          "linkedInDistributionTarget" => new stdClass()
        ),
        "owner" => "urn:li:organization:$orgID",
        "text" => array(
          "text" =>  $text
        )
      );
    } else {
      $data = array(
        "owner" => "urn:li:company:$orgID",
        "text" => array(
          "text" =>  $text
        ),
        "distribution" => array(
          "linkedInDistributionTarget" => new stdClass()
        ),
        "content" => array(
          "contentEntities" => $this->buildOrgMediaObjectArray($uploadedContents),
          "title" => "test",
          "shareMediaCategory" => "IMAGE"
        ),


      );
    }

    return $data;
  }

  public function buildOrgMediaObjectArray($uploadedContents)
  {
    $contents = array();
    foreach ($uploadedContents as $contentID) {
      array_push($contents, ["entity" => $contentID]);
    }
    return $contents;
  }

  public function buildMediaObjectArray($uploadedContents)
  {
    $contents = array();
    foreach ($uploadedContents as $contentID) {
      $data =
        array(
          'status' => 'READY',
          'description' =>
          array(
            'text' => 'Center stage!',
          ),
          'media' => $contentID,
          'title' =>
          array(
            'text' => 'LinkedIn Talent Connect 2018',
          ),
        );

      array_push($contents, $data);
    }

    return $contents;
  }

  public function remove($company_id)
  {
    $input["id"] = $company_id;

    $validation = Validator::make($input, [
      'id' => ['required', 'exists:linkedin_accounts,company_id']
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

    // $gs = Gs::first();
    // $company = Company::where('id', $company_id)->first();
    // // return $company->removed['linkedin'];
    // if ($company->removed >= $gs->remove_social_media) {
    //   return response()->json(['status' => 'failure', "error" => "You cant remove a social account more than " . $gs->remove_social_media . " times on this plan"]);
    // }

    // $company = Company::where('id', $company_id)->update([
    //   "removed" => $company->removed + 1
    // ]);

    LinkedinAccount::where('company_id', $company_id)->delete();

    $sub->remove_social -= 1;
    $sub->save();

    return response()->json(['status' => 'success', 'msg' => 'Linkedin account successfully deleted']);
  }

  public function test()
  {
    // $gs = Plan::create([
    //   'name' => 'Freemium',
    //   'clients' => 5,
    //   'posts' => 20,
    //   'accounts' => 3,
    //   'days' => 14,
    //   'price' => 0,
    //   'remove_social' => 2
    // ]);
    // Plan::create([
    //   'name' => 'Starter',
    //   'clients' => 7,
    //   'posts' => 100,
    //   'accounts' => 2,
    //   'days' => 30,
    //   'price' => 2999,
    //   'remove_social' => 2
    // ]);
    // Plan::create([
    //   'name' => 'Basic',
    //   'clients' => 15,
    //   'posts' => 60,
    //   'accounts' => 5,
    //   'days' => 14,
    //   'price' => 3999,
    //   'remove_social' => 4
    // ]);
    // Plan::create([
    //   'name' => 'Plus',
    //   'clients' => 16,
    //   'posts' => 80,
    //   'accounts' => 6,
    //   'days' => 30,
    //   'price' => 6999,
    //   'remove_social' => 5
    // ]);
    // Plan::create([
    //   'name' => 'Enterprise',
    //   'clients' => 20,
    //   'posts' => 100,
    //   'accounts' => 7,
    //   'days' => 30,
    //   'price' => 5000,
    //   'remove_social' => 6
    // ]);

    Enterprise::create([
      'name' => 'PPD',
    ]);
    Enterprise::create([
      'name' => 'PPC',
    ]);
    Enterprise::create([
      'name' => 'TNC',
    ]);
    // $url = route('unauthorized');
    return 'yes';
    // return date('d, M. Y', strtotime(Auth::User()->ended_at));
    // return Carbon::parse(Auth::User()->ended_at)->toDateString();
    // // $user = User::where('id', 2)->first();
    // $user = User::find(2);
    // // return response()->json($user->started_at);
    // // dd($user);
    // $start_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
    // $end_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->ended_at);
    // $different_days = $start_date->diffInDays($end_date);

    // dd($different_days);
  }
}
