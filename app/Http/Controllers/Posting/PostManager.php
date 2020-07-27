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
        //    $this->middleware('auth');
        // Auth::loginUsingId(6);
        Auth::loginUsingId(4);
    }

    public function CreatePost(Request $request)
    {
        $input = $request->all();
        $input["user_id"] = Auth::user()->id;

        $validation = Validator::make($input, [
            'company_id' => ['required', 'integer'],
            'content' => ['required', 'string'],
            'media' => ['array'],
            'media.*' => ['required', 'string'],
            'platforms' => ['required', 'array'],
            'platforms.*' => ['required', 'string'],
            'schedule_date' => ['integer'],
            'hashtag' => ['string']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        if (empty($input["media"])) {
            $input["media"] = [];
        }
        // $post = $input;
        $post = Post::create($input);

        if (!isset($input["schedule_date"]) || $input["schedule_date"] == NULL) {
            foreach ($input["platforms"] as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        break;
                }
            }
            $post->update(["is_posted" => true]);
        }

        return response()->json(['status' => 'success', 'post' => $post]);
    }

    public function GetPosts(Request $request)
    {
        $user = Auth::user();

        $posts = Post::where("user_id", $user->id)->where('is_posted', '=', true)->get();

        return response()->json(['status' => 'success', 'posts' => $posts]);
    }

    public function GetScheduledPosts(Request $request)
    {
        $user = Auth::user();

        $posts = Post::where("user_id", $user->id)->where('schedule_date', '!=', '')->where('is_posted', '!=', true)->get();

        return response()->json(['status' => 'success', 'posts' => $posts]);
    }

    //upload featured media
    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media.*' => 'required|mimes:3gp,mp4,avi,mov,jpeg,png,jpg,gif,svg|max:20480',
        ]);

        //return errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $names = array();

        foreach ($request->file('media') as $media) {
            $name = time() . mt_rand(1, 9999) . '.' . $media->getClientOriginalExtension();
            $destinationPath = public_path(Utils::UPLOADS_DIR);
            $media->move($destinationPath, $name);

            array_push($names, $name);
        }

        return response()->json(['success' => ["message" => "Media uploaded successfuly", "media_path" => $names]]);
    }

    public function DeletePost($id)
    {
        $post = Post::find($id);

        if ($post == NULL) {
            return response()->json(['status' => 'failure', 'message' => 'post does not exist']);
        }

        Post::destroy($id);

        return response()->json(['success' => 'Post deleted']);
    }

    public function UpdatePost(Request $request)
    {
        $input = $request->all();
        // $input["user_id"] = Auth::user()->id;

        $validation = Validator::make($input, [
            'company_id' => ['required', 'integer'],
            'content' => ['required', 'string'],
            'media' => ['array'],
            'media.*' => ['required', 'string'],
            'platforms' => ['required', 'array'],
            'platforms.*' => ['required', 'string'],
            'schedule_date' => ['integer'],
            'hashtag' => ['string']
        ]);




        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data);
        }

        // if (empty($input["media"])) {
        //     $input["media"] = [];
        // }
        // $post = $input;
        $post = Post::where('id', $request->post_id)->first();
        $post->update($input);
        if (!isset($input["schedule_date"]) || $input["schedule_date"] == NULL) {
            foreach ($input["platforms"] as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        break;
                }
            }
            $post->update(["is_posted" => true]);
        }

        return response()->json(['status' => 'success', 'post' => $post]);
    }

    public function scheduler()
    {
        $date = \Carbon\Carbon::now();
        $date->setTimezone("Africa/Lagos");

        $posts = Post::where('schedule_date', '<=', $date->timestamp)->where('is_posted', '!=', true)->get();

        foreach ($posts as $post) {
            foreach ($post->platforms as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        print("posted to linkedin");
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        print("posted to twitter");
                        break;
                }
            }

            $post->update(["is_posted" => true]);
        }

        die();
    }
}
