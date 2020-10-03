<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use \Mailjet\Resources;

class CronJobController extends Controller
{
    public function subscriptionReminder()
    {
        $mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'), true, ['version' => 'v3.1']);
        $users = User::all();
        // $day7 = array();
        foreach ($users as $user) {
            if ($user->daysLeft == 7 || $user->daysLeft == 3 || $user->daysLeft == 24) {
                // array_push($day7, $user);

                $html = file_get_contents(resource_path('views/emails/subscription.blade.php'));
                $html = str_replace(
                    ['{{NAME}}', '{{PLAN}}', '{{DAYS}}'],
                    [$user->name, $user->plan->name, $user->daysLeft],
                    $html
                );
                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => "info@digifigs.com",
                                'Name' => "Postlate"
                            ],
                            'To' => [
                                [
                                    'Email' => $user->email,
                                    'Name' => $user->name
                                ]
                            ],
                            'Subject' => "Subscription Reminder",
                            'TextPart' => "Subscription Reminder",
                            'HTMLPart' => $html,
                            'CustomID' => "AppGettingStartedTest"
                        ]
                    ]
                ];
                $response = $mj->post(Resources::$Email, ['body' => $body]);
            }
        }
        return response()->json('success');
    }
}
