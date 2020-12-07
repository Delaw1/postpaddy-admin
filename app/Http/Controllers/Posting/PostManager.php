<?php

namespace App\Http\Controllers\Posting;

use \App\Http\Controllers\SocialMedia\LinkedinController;
use \App\Http\Controllers\SocialMedia\TwitterController;
use \App\Http\Controllers\SocialMedia\FacebookController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\User;
use \App\Utils;
use \App\Post;
use App\Subscription;
use \App\Http\Controllers\UserController;
use Illuminate\Support\Facades\File;

class PostManager extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function CreatePost(Request $request)
    {
        $userController = new UserController();
        $sub = $userController->checkSubcription();
        // Check active subscription
        if (!$sub) {
            return response()->json(['status' => 'failure', 'error' => 'Subscription expired, upgrade your plan']);
        }

        $input = $request->all();
        // $input["user_id"] = 4;
        $input["user_id"] = Auth::user()->id;

        $validation = Validator::make($input, [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'content' => ['required', 'string'],
            'media' => ['array'],

            'platforms' => ['required', 'array'],
            'schedule_date' => ['integer']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data, 400);
        }

        $checkPostStatus = $userController->checkPostStatus($sub, ['company_id' => $input['company_id']]);

        if (!$checkPostStatus) {
            return response()->json(['status' => 'failure', 'error' => 'Minimum number of allowed post exceeded, Upgrade you account']);
        }

        if (empty($input["media"])) {
            $input["media"] = [];
        }

        $post = Post::create($input);

        $reducePost = $userController->reducePost($sub, ['company_id' => $input['company_id']]);
        // $sub->posts -= 1;
        // $sub->save();

        if (!isset($input["schedule_date"]) || $input["schedule_date"] == NULL) {
            foreach (array_keys($input["platforms"]) as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        break;
                    case "facebook":
                        (new FacebookController())->postNow($post);
                        break;
                }
            }
            $post->update(["is_posted" => true]);
        }

        return response()->json(['status' => 'success', 'post' => $post], 201);
    }

    public function GetPosts(Request $request)
    {
        $user = Auth::user();

        $posts = Post::where("user_id", $user->id)->where('is_posted', true)->get();
        foreach ($posts as $post) {
            $post['company'] = $post->Company;
            // unset($post['company_id']);
        }
        return response()->json(['status' => 'success', 'posts' => $posts]);
    }

    public function GetScheduledPosts(Request $request)
    {
        $user = Auth::user();

        $posts = Post::where("user_id", $user->id)->where('schedule_date', '!=', '')->where('is_posted', '!=', true)->get();

        foreach ($posts as $post) {
            $post['company'] = $post->Company;
            // unset($post['company_id']);
        }

        return response()->json(['status' => 'success', 'posts' => $posts]);
    }

    //upload featured media
    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media.*' => 'required|mimes:3gp,mp4,avi,mov,jpeg,png,jpg,gif,svg|max:204800',
        ]);

        //return errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $names = array();

        foreach ($request->file('media') as $media) {
            $name = 'postpaddy' . time() . mt_rand(1, 9999) . '.' . $media->getClientOriginalExtension();
            $destinationPath = public_path(Utils::UPLOADS_DIR);
            $media->move($destinationPath, $name);

            array_push($names, $name);
        }

        return response()->json(['success' => ["message" => "Media uploaded successfuly", "media_path" => $names]], 200);
    }

    public function DeletePost($id)
    {
        $post = Post::find($id);

        if ($post == NULL) {
            return response()->json(['status' => 'failure', 'message' => 'post does not exist'], 404);
        }

        Post::destroy($id);

        return response()->json(['success' => 'Post deleted']);
    }

    public function UpdatePost(Request $request)
    {
        $input = $request->all();
        // $input["user_id"] = Auth::user()->id;

        // 'media.*' => ['required', 'string'],
        // 'platforms.*' => ['required', 'string'],

        $validation = Validator::make($input, [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'content' => ['required', 'string'],
            'media' => ['array'],

            'platforms' => ['required', 'array'],

            'schedule_date' => ['integer']
        ]);

        if ($validation->fails()) {
            $data = json_decode($validation->errors(), true);

            $data = ['status' => 'failure']  + $data;

            return response()->json($data, 400);
        }

        // if (empty($input["media"])) {
        //     $input["media"] = [];
        // }
        // $post = $input;
        $post = Post::where('id', $request->input('post_id'))->first();

        if (is_array($request->input('media'))) {
            $old_media = array_diff($post->media, $request->input('media'));

            $old_media_path = array();
            foreach ($old_media as $media) {
                array_push($old_media_path, public_path(Utils::UPLOADS_DIR) . '/' . $media);
            }

            File::delete($old_media_path);
        }

        $post->update($input);
        
        if ($post["schedule_date"] == 0 || $post["schedule_date"] == NULL) {
            foreach (array_keys($post["platforms"]) as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        break;
                    case "facebook":
                        (new FacebookController())->postNow($post);
                        break;
                }
            }
            $post->update(["is_posted" => true]);
        }

        return response()->json(['status' => 'success', 'post' => $post], 200);
    }

    public function test()
    {
        $date = \Carbon\Carbon::now();
        $date->setTimezone("Africa/Lagos");
        $posts = Post::where('schedule_date', '!=', '')->where('schedule_date', '<=', $date->timestamp*1000)->where('is_posted', 0)->get();
        return response()->json([$posts, $date->timestamp]);
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
