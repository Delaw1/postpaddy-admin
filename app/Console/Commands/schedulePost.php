<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Post;
use \App\Http\Controllers\Posting\PostManager;
use \App\Http\Controllers\SocialMedia\LinkedinController;
use \App\Http\Controllers\SocialMedia\TwitterController;
use \App\Http\Controllers\SocialMedia\FacebookController;

class schedulePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post scheduled';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = \Carbon\Carbon::now();
        $date->setTimezone("Africa/Lagos");

        $posts = Post::where('schedule_date', '!=', '')->where('schedule_date', '<=', $date->timestamp*1000)->where('is_posted', 0)->get();

        foreach ($posts as $post) {
            foreach (array_keys($post->platforms) as $platform) {
                switch ($platform) {
                    case "linkedin":
                        (new LinkedinController())->postNow($post);
                        // print("posted to linkedin");
                        break;
                    case "twitter":
                        (new TwitterController())->postNow($post);
                        // print("posted to twitter");
                        break;
                    case "facebook":
                        (new FacebookController())->postNow($post);
                        break;
                }
            }
            $post->update(["is_posted" => true]);
        }
        $this->info("Scheduled post sent");
    }
}
