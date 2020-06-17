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
    public const UPLOADS_DIR= 'uploads';
    
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function CreatePost(Request $request)
    {
        $input = $request->all();
        $input["user_id"] = Auth::user()->id;

        $validation = Validator::make($input, [
           // 'company_id' => ['required', 'integer', 'exists:posts'],
            'content' => ['required', 'string'],
            'media' => ['array'],
            'media.*'=>['required', 'string'],
            'platforms' => ['required', 'array'],
            'platforms.*'=>['required', 'string'],
            'schedule_date' => ['integer']
        ]);

        if($validation->fails())
        {
            $data = json_decode($validation->errors(), true);
            
            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        if(empty($input["media"])){$input["media"] = "[]";}
        $post = Post::create($input);

        return response()->json(['status' => 'success', 'post'=>$post] );
    }

    public function GetPosts(Request $request){
        $user = Auth::user();

        $posts = Post::where("user_id", $user->id)->get();

        return response()->json(['status' => 'success', 'posts'=>$posts] );
    }

    //upload featured media
    public function uploadMedia(Request $request){
        $validator = Validator::make( $request->all(), [
            'media' => 'required|mimes:3gp,mp4,avi,mov,jpeg,png,jpg,gif,svg|max:20480',
        ]);

        //return errors if any
        if ( $validator->fails() ) {
            return response()->json( ['error'=>$validator->errors()], 422 );
        }
        
        $media = $request->file('media');
        $name = time().'.'.$media->getClientOriginalExtension();
        $destinationPath = public_path(self::UPLOADS_DIR);
        $media->move($destinationPath, $name);

        return response()->json( ['success'=>["message" => "Media uploaded successfuly", "media_path"=>$name]] );
    }
}
