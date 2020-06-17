<?php

namespace App\Http\Controllers\Posting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use \App\User;
use \App\Post;

class PostManager extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function CreatePost(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'company_id' => ['required', 'integer', 'exists:posts'],
            'content' => ['required', 'string'],
            'media' => ['array'],
            'media.*'=>['required', 'string'],
            'platforms' => ['array'],
            'platforms.*'=>['required', 'string'],
            'schedule_date' => ['date:format:dd/mm/Y']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        $post = Post::create($input);

        return response()->json(['status' => 'success', 'post'=>$post] );
    }
}
