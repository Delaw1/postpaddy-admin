<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function createAdmin(Request $request) {
        if(Auth::user()->role == 'super_admin') {
            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'email_verified_at' => Carbon::now(),
                'role' => 'admin',
                'plan_id' => 1
            ]);
            
            return response()->json($user);
        }
        else {
            return response()->json(['error' => 'Permission not allowed']);
        } 
    }

    public function getUsers() {
        return response()->json(["status" => "failure", "message" => "test"]);
        // $users = User::all();
        // return response()->json($users);
    }

    public function getUser($id) {
        $user = User::where('id', $id)->get();
        return response()->json($user);
    }
}
