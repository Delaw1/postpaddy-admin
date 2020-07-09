<?php

namespace App\Http\Controllers\Posting;

use \App\Http\Controllers\SocialMedia\LinkedinController;
use \App\Http\Controllers\SocialMedia\TwitterController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\User;
use \App\Utils;
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

        if(empty($input["media"])){$input["media"] = [];}
        $post = Post::create($input);

        foreach($input["platforms"] as $platform){
            switch($platform){
                case "linkedin": (new LinkedinController())->postNow($post); break;
                case "twitter": (new TwitterController())->postNow($post); break;
            }
        }

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
            'media.*' => 'required|mimes:3gp,mp4,avi,mov,jpeg,png,jpg,gif,svg|max:20480',
        ]);

        //return errors if any
        if ( $validator->fails() ) {
            return response()->json( ['error'=>$validator->errors()], 422 );
        }
        
        $names = array();

        foreach( $request->file('media') as $media ){  
            $name = time().mt_rand(1, 9999).'.'.$media->getClientOriginalExtension();
            $destinationPath = public_path(Utils::UPLOADS_DIR);
            $media->move($destinationPath, $name);

            array_push( $names, $name);
        }

        return response()->json( ['success'=>["message" => "Media uploaded successfuly", "media_path"=>$names]] );
    }
}
