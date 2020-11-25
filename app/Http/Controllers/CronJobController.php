<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use \Mailjet\Resources;
use App\Notification;
use Carbon\Carbon;

class CronJobController extends Controller
{
    public function subscriptionReminder()
    {
        $mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'), true, ['version' => 'v3.1']);
        $users = User::all();
        // $day7 = array();
        foreach ($users as $user) {
            if ($user->daysLeft == 7 || $user->daysLeft == 3 || $user->daysLeft == 1) {
                // array_push($day7, $user);
                Notification::create([
                    'user_id' => $user->id,
                    'message' => "Your subscription to the " . $user->plan->name . " will expire soon, which means your access to the " . $user->plan->name . " features would be cancelled soon. Only " . $user->daysLeft . " days left."

                ]);

                if ($user->plan_id == 1) {
                    $html = file_get_contents(resource_path('views/emails/reminder_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}', '{{DAYS}}', '{{PRICE}}', '{{DATE}}'],
                        [$user->last_name." ".$user->first_name, $user->plan->name, $user->daysLeft, $user->plan->price, Carbon::parse($user->ended_at)->format('d M')],
                        $html
                    );
                } else {
                    $html = file_get_contents(resource_path('views/emails/reminder.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}', '{{DAYS}}', '{{PRICE}}', '{{DATE}}'],
                        [$user->last_name." ".$user->first_name, $user->plan->name, $user->daysLeft, $user->plan->price, Carbon::parse($user->ended_at)->format('d M')],
                        $html
                    );
                }

                // return $html;


                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => "info@digifigs.com",
                                'Name' => "PostPaddy"
                            ],
                            'To' => [
                                [
                                    'Email' => $user->email,
                                    'Name' => $user->last_name." ".$user->first_name
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
            // return response()->json($day7);
            if ($user->daysLeft <= 0 && $user->expired == 0) {
                if ($user->plan_id == 1) {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => "Your subscription to the " . $user->plan->name . " has expired, which means your access to the " . $user->plan->name . " features has been cancelled."

                    ]);

                    $html = file_get_contents(resource_path('views/emails/expired_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}', '{{DAYS}}'],
                        [$user->last_name." ".$user->first_name, $user->plan->name, $user->daysLeft],
                        $html
                    );
                    // return $html;
                    $body = [
                        'Messages' => [
                            [
                                'From' => [
                                    'Email' => "info@digifigs.com",
                                    'Name' => "PostPaddy"
                                ],
                                'To' => [
                                    [
                                        'Email' => $user->email,
                                        'Name' => $user->last_name." ".$user->first_name
                                    ]
                                ],
                                'Subject' => "Subscription Expired",
                                'TextPart' => "Subscription Expired",
                                'HTMLPart' => $html,
                                'CustomID' => "AppGettingStartedTest"
                            ]
                        ]
                    ];
                    $response = $mj->post(Resources::$Email, ['body' => $body]);
                } else {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => "Your subscription to the " . $user->plan->name . " has expired, which means your access to the " . $user->plan->name . " features has been cancelled."

                    ]);

                    $html = file_get_contents(resource_path('views/emails/expired.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}', '{{DAYS}}'],
                        [$user->last_name." ".$user->first_name, $user->plan->name, $user->daysLeft],
                        $html
                    );
                    return $html;
                    $body = [
                        'Messages' => [
                            [
                                'From' => [
                                    'Email' => "info@digifigs.com",
                                    'Name' => "PostPaddy"
                                ],
                                'To' => [
                                    [
                                        'Email' => $user->email,
                                        'Name' => $user->last_name." ".$user->first_name
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
                $user->expired = true;
                $user->save();
            }
        }
        return response()->json('success');
    }
}
