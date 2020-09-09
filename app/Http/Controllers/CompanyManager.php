<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use \App\Company;
use \Mailjet\Resources;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;
use App\Gs;
use \App\Utils;
use \App\Http\Controllers\SocialMedia\LinkedinController;
use \App\Http\Controllers\SocialMedia\TwitterController;
use \App\Http\Controllers\SocialMedia\FacebookController;

class CompanyManager extends Controller
{
    public function __construct()
    {
        // Auth::loginUsingId(4);
        $this->middleware( 'auth' );
    }

    public function CreateCompany(Request $request)
    {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;

        $validation = Validator::make($input, [
            'name' => ['required', 'string'],
            'email_address' => ['required', 'string', 'email', 'unique:companies'],
            'profile_img' => 'image|mimes:jpeg,png,jpg,gif,sng|max:2048',
            'category' => 'required|string'
        ]);

        if(Company::where(['user_id' => Auth::user()->id, 'name' => $input['name']])->count() > 0) {
            return response()->json(['status' => 'failure', 'error' => "Client name already exist"]); 
        }

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }
        // $baseClient = Gs::first()->clients;
        // $userClient = Company::where("user_id", Auth::user()->id)->count();
        // if($userClient >= $baseClient) {
        //     return response()->json(['status' => 'failure', 'message' => 'Minimum number of client exceeded, Upgrade you account']);
        // }

        if ($request->hasFile('profile_img')) {
            $name = time() . mt_rand(1, 9999) . '.' . $request->file('profile_img')->getClientOriginalExtension();
            $destinationPath = public_path(Utils::PROFILE_IMG_DIR);
            $request->file('profile_img')->move($destinationPath, $name);
            $input['image'] = $name;
        }

        $company = Company::create($input);

        return response()->json(['status' => 'success', 'company' => $company]);
    }

    public function GetCompanies(Request $request)
    {
        $user = Auth::user();

        $companies = Company::where('user_id', $user->id)->get();

        return response()->json(['status' => 'success', 'companies' => $companies]);
    }

    public function GetCompany($id)
    {
        $company = Company::find($id);

        if ($company == NULL) {
            return response()->json(['status' => 'failure', 'message' => 'company does not exist']);
        }

        return response()->json(['status' => 'success', 'companies' => $company]);
    }

    public function DeleteCompany($id)
    {
        $company = Company::find($id);

        if ($company == NULL) {
            return response()->json(['status' => 'failure', 'message' => 'company does not exist']);
        }

        Company::destroy($id);

        return response()->json(['success' => 'Company deleted']);
    }


    public function UpdateCompany(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'id' => ['required', 'exists:companies']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $company = Company::find($input['id']);

        if ($request->hasFile('profile_img')) {
            $name = time() . mt_rand(1, 9999) . '.' . $request->file('profile_img')->getClientOriginalExtension();
            $destinationPath = public_path(Utils::PROFILE_IMG_DIR);
            $request->file('profile_img')->move($destinationPath, $name);
            $request['image'] = $name;
        }

        $company->update($request->all());

        return response()->json(['success' => 'Company updated']);
    }

    public function RemoveSocialMedia(Request $request) {
        $input['id'] = $request->company_id;

        $validation = Validator::make($input, [
            'id' => ['required', 'exists:companies']
        ]);

        if ($validation->fails()) {
            // $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure', 'error' => $validation->errors()->first()];

            return response()->json($data, 400);
        }
        $platform = $request->platform;
        switch($platform) {
            case "linkedin": 
                return (new LinkedinController())->remove($request->company_id);
                break;
            case "twitter":
                return (new TwitterController())->remove($request->company_id);
                break;
            case "facebook":
                return (new FacebookController())->remove($request->company_id);
                break;
            default:
                return response()->json(["error" => "Platform field is invalid"], 400);
        }

    }

    public function socialMedia($id)
    {
        $platform = Company::where('id', $id)->first();
        return response()->json(['status' => 'success', 'platform' => $platform->platforms]);
    }



    public function sendMail(Request $request)
    {
       

        $subject = "Geonel";
        $view = 'emails.geonel';
        $email =  $request->input('email_to');
        $mailBody = new MyMail($subject, $request->all(), $view);
        $mail = Mail::to($email)->send($mailBody);

        if ($mail) {
            return true;
        }
        return false;
    }
}
