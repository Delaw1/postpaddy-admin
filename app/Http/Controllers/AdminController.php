<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EnterprisePayment;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function setEnterprisePackage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'enterprise_id' => ['required', 'exists:enterprises'],
            'user_id' => ['required', 'exists:users'],
            'clients' => ['required'],
            'posts' => ['required'],
            'remove_social' => ['required'],
            'price' => ['required']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $enterprise = EnterprisePayment::create($request->all());
        return response()->json(['status' => 'success', 'data' => $enterprise]);
    }
}
