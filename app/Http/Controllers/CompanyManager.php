<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use \App\User;
use \App\Company;

class CompanyManager extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function CreateCompany(Request $request)
    {
        $input = $request->all();
        $input["user_id"] = Auth::user()->id;

        $validation = Validator::make($input, [
            'name' => ['required', 'string', 'unique:companies']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $company = Company::create($input);

        return response()->json(['status' => 'success', 'company'=>$company] );
    }

    public function GetCompanies(Request $request){
        $user = Auth::user();

        $companies = Company::where("user_id", $user->id)->get();

        return response()->json(['status' => 'success', 'companies'=>$companies] );
    }
}