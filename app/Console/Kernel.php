<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Post;
use \App\Http\Controllers\Posting\PostManager;
use \App\Http\Controllers\SocialMedia\LinkedinController;
use \App\Http\Controllers\SocialMedia\TwitterController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        $schedule->call('App\Http\Controllers\PostManager@scheduler')->everyMinute();
        // $schedule->call(function () {
        //     // (new PostManager())->scheduler();
        //     // Post::where('id', 35)->delete();
        //     $date = \Carbon\Carbon::now();
        //     $date->setTimezone("Africa/Lagos");

        //     $posts = Post::where('schedule_date', '<=', $date->timestamp)->where('is_posted', '!=', true)->get();

        //     foreach ($posts as $post) {
        //         foreach ($post->platforms as $platform) {
        //             switch ($platform) {
        //                 case "linkedin":
        //                     (new LinkedinController())->postNow($post);
        //                     print("posted to linkedin");
        //                     break;
        //                 case "twitter":
        //                     (new TwitterController())->postNow($post);
        //                     print("posted to twitter");
        //                     break;
        //             }
        //         }

        //         $post->update(["is_posted" => true]);
        //     }
        // })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
