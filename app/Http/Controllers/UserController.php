<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use \App\Utils;

class UserController extends Controller
{
    public function __construct()
    {
        // Auth::loginUsingId(4);
        $this->middleware('auth');
    }

    public function EditProfile(Request $request) {
        $input = $request->all();

        $validation = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            
        ]); 

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json(['status' => 'failure', 'error' => $validation->errors()->first()], 400);
        }

        

        if ($request->hasFile('profile_img')) {
            $name = time() . mt_rand(1, 9999) . '.' . $request->file('profile_img')->getClientOriginalExtension();
            $destinationPath = public_path(Utils::PROFILE_IMG_DIR);
            $request->file('profile_img')->move($destinationPath, $name);
            $input['image'] = $name;
        }
        
        if($request->has('profile_img') && $request->profile_img == null) {
            $input['image'] = null;
        }

        $user = User::find(Auth::User()->id)->update($input);
        
        if($user) {
            return response()->json(['status' => 'success', 'msg' => 'Profile successfully updated']);
        }
        return response()->json(['status' => 'failure', 'error' => 'Network Error']);
    }

    public function GetUser() {
        return response()->json(Auth::User());
    }
}
