<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;
use \Mailjet\Resources;
use App\User;
use App\Notification;
use Carbon\Carbon;

class EmailController extends Controller
{
    private $mj;

    public function __construct()
    {
        $this->mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'), true, ['version' => 'v3.1']);
    }
    public function sendMail(Request $request)
    {
        $subject = "Geonel";
        $view = 'emails.geonel';
        $email =  $request->input('email_to');
        $email_from = $request->input('email_tofrom');
        $mailBody = new MyMail($subject, $request->all(), $view);
        $mail = Mail::to($email)->send($mailBody);

        if ($mail) {
            return true;
        }
        return false;
    }

    public function sendSubscriptionEmail(array $data)
    {
        $html = file_get_contents(resource_path('views/emails/subscription.blade.php'));
        $html = str_replace(
            ['{{NAME}}', '{{PLAN}}'],
            [$data['name'], $data['plan_name']],
            $html
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => env("MAILJET_FROM"),
                        'Name' => "PostPaddy"
                    ],
                    'To' => [
                        [
                            'Email' => $data['email'],
                            'Name' => $data['name']
                        ]
                    ],
                    'Subject' => "Subscription successful",
                    'TextPart' => "Subscription successful",
                    'HTMLPart' => $html,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $this->mj->post(Resources::$Email, ['body' => $body]);
    }

    public function sendVerificationEmail(array $data)
    {
        $html = file_get_contents(resource_path('views/emails/welcomemail.blade.php'));
        $html = str_replace(
            ['{{NAME}}', '{{VERIFY_LINK}}', '{{EMAIL}}'],
            [$data['name'], "https://postpaddy.com/api/VerifyEmail/" . base64_encode($data['email']), $data['email']],
            $html
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => env("MAILJET_FROM"),
                        'Name' => "PostPaddy"
                    ],
                    'To' => [
                        [
                            'Email' => $data['email'],
                            'Name' => $data['name']
                        ]
                    ],
                    'Subject' => "Welcome to PostPaddy",
                    'TextPart' => "Welcome to PostPaddy",
                    'HTMLPart' => $html,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $this->mj->post(Resources::$Email, ['body' => $body]);
    }

    public function sendPasswordResetEmail(array $data)
    {
        $email = $data["email"];
        $token = base64_encode($email);

        $html = file_get_contents(resource_path('views/emails/passwordreset.blade.php'));
        $html = str_replace(
            ['{{TOKEN}}', '{{NAME}}'],
            [$token, $data["name"]],
            $html
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => env("MAILJET_FROM"),
                        'Name' => "PostPaddy"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                        ]
                    ],
                    'Subject' => "PostPaddy Password Reset",
                    'TextPart' => "PostPaddy Password Reset",
                    'HTMLPart' => $html,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $this->mj->post(Resources::$Email, ['body' => $body]);
    }

    public function subscriptionReminder()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // if ($user->daysLeft == 7 || $user->daysLeft == 3 || $user->daysLeft == 1) {
            //     // array_push($day7, $user);
            //     Notification::create([
            //         'user_id' => $user->id,
            //         'message' => "Your subscription to the " . $user->plan->name . " will expire soon, which means your access to the " . $user->plan->name . " features would be cancelled soon. Only " . $user->daysLeft . " days left."

            //     ]);

            //     if ($user->plan_id == 1) {
            //         $html = file_get_contents(resource_path('views/emails/reminder_free.blade.php'));
            //         $html = str_replace(
            //             ['{{NAME}}', '{{PLAN}}', '{{DAYS}}', '{{PRICE}}', '{{DATE}}'],
            //             [$user->last_name." ".$user->last_name, $user->plan->name, $user->daysLeft, $user->plan->price, Carbon::parse($user->ended_at)->format('d M')],
            //             $html
            //         );
            //     } else {
            //         $html = file_get_contents(resource_path('views/emails/reminder.blade.php'));
            //         $html = str_replace(
            //             ['{{NAME}}', '{{PLAN}}', '{{DAYS}}', '{{PRICE}}', '{{DATE}}'],
            //             [$user->last_name." ".$user->last_name, $user->plan->name, $user->daysLeft, $user->plan->price, Carbon::parse($user->ended_at)->format('d M')],
            //             $html
            //         );
            //     }

            //     $body = [
            //         'Messages' => [
            //             [
            //                 'From' => [
            //                     'Email' => env("MAILJET_FROM"),
            //                     'Name' => "PostPaddy"
            //                 ],
            //                 'To' => [
            //                     [
            //                         'Email' => $user->email,
            //                         'Name' => $user->last_name." ".$user->last_name
            //                     ]
            //                 ],
            //                 'Subject' => "Subscription Reminder",
            //                 'TextPart' => "Subscription Reminder",
            //                 'HTMLPart' => $html,
            //                 'CustomID' => "AppGettingStartedTest"
            //             ]
            //         ]
            //     ];
            //     $this->mj->post(Resources::$Email, ['body' => $body]);
            // }

            if($user->daysLeft == 7) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => "Your subscription to the " . $user->plan->name . " will expire soon, which means your access to the " . $user->plan->name . " features would be cancelled soon. Only " . $user->daysLeft . " days left."

                ]);

                if ($user->plan_id == 1) {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_7days_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}'],
                        [$user->first_name." ".$user->last_name],
                        $html
                    );
                } else {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_7days.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}'],
                        [$user->first_name." ".$user->last_name, $user->plan->name],
                        $html
                    );
                }

                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => env("MAILJET_FROM"),
                                'Name' => "PostPaddy"
                            ],
                            'To' => [
                                [
                                    'Email' => $user->email,
                                    'Name' => $user->first_name." ".$user->last_name
                                ]
                            ],
                            'Subject' => "Subscription Reminder",
                            'TextPart' => "Subscription Reminder",
                            'HTMLPart' => $html,
                            'CustomID' => "AppGettingStartedTest"
                        ]
                    ]
                ];
                $this->mj->post(Resources::$Email, ['body' => $body]);
            }

            if($user->daysLeft == 3) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => "Your subscription to the " . $user->plan->name . " will expire soon, which means your access to the " . $user->plan->name . " features would be cancelled soon. Only " . $user->daysLeft . " days left."

                ]);

                if ($user->plan_id == 1) {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_3days_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}'],
                        [$user->first_name." ".$user->last_name],
                        $html
                    );
                } else {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_3days.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}'],
                        [$user->first_name." ".$user->last_name, $user->plan->name],
                        $html
                    );
                }

                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => env("MAILJET_FROM"),
                                'Name' => "PostPaddy"
                            ],
                            'To' => [
                                [
                                    'Email' => $user->email,
                                    'Name' => $user->first_name." ".$user->last_name
                                ]
                            ],
                            'Subject' => "Subscription Reminder",
                            'TextPart' => "Subscription Reminder",
                            'HTMLPart' => $html,
                            'CustomID' => "AppGettingStartedTest"
                        ]
                    ]
                ];
                $this->mj->post(Resources::$Email, ['body' => $body]);
            }

            if($user->daysLeft == 1) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => "Your subscription to the " . $user->plan->name . " will expire soon, which means your access to the " . $user->plan->name . " features would be cancelled soon. Only " . $user->daysLeft . " days left."

                ]);

                if ($user->plan_id == 1) {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_1day_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}'],
                        [$user->first_name." ".$user->last_name],
                        $html
                    );
                } else {
                    $html = file_get_contents(resource_path('views/emails/plan_expiry_1day.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}'],
                        [$user->first_name." ".$user->last_name, $user->plan->name],
                        $html
                    );
                }

                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => env("MAILJET_FROM"),
                                'Name' => "PostPaddy"
                            ],
                            'To' => [
                                [
                                    'Email' => $user->email,
                                    'Name' => $user->first_name." ".$user->last_name
                                ]
                            ],
                            'Subject' => "Subscription Reminder",
                            'TextPart' => "Subscription Reminder",
                            'HTMLPart' => $html,
                            'CustomID' => "AppGettingStartedTest"
                        ]
                    ]
                ];
                $this->mj->post(Resources::$Email, ['body' => $body]);
            }

            if ($user->daysLeft <= 0 && $user->expired == 0) {
                if ($user->plan_id == 1) {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => "Your subscription to the " . $user->plan->name . " has expired, which means your access to the " . $user->plan->name . " features has been cancelled."

                    ]);

                    $html = file_get_contents(resource_path('views/emails/expired_free.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}'],
                        [$user->first_name." ".$user->last_name],
                        $html
                    );
                    
                    $body = [
                        'Messages' => [
                            [
                                'From' => [
                                    'Email' => env("MAILJET_FROM"),
                                    'Name' => "PostPaddy"
                                ],
                                'To' => [
                                    [
                                        'Email' => $user->email,
                                        'Name' => $user->first_name." ".$user->last_name
                                    ]
                                ],
                                'Subject' => "Subscription Expired",
                                'TextPart' => "Subscription Expired",
                                'HTMLPart' => $html,
                                'CustomID' => "AppGettingStartedTest"
                            ]
                        ]
                    ];
                    $this->mj->post(Resources::$Email, ['body' => $body]);
                } else {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => "Your subscription to the " . $user->plan->name . " has expired, which means your access to the " . $user->plan->name . " features has been cancelled."

                    ]);

                    $html = file_get_contents(resource_path('views/emails/expired.blade.php'));
                    $html = str_replace(
                        ['{{NAME}}', '{{PLAN}}'],
                        [$user->first_name." ".$user->last_name, $user->plan->name],
                        $html
                    );

                    $body = [
                        'Messages' => [
                            [
                                'From' => [
                                    'Email' => env("MAILJET_FROM"),
                                    'Name' => "PostPaddy"
                                ],
                                'To' => [
                                    [
                                        'Email' => $user->email,
                                        'Name' => $user->first_name." ".$user->last_name
                                    ]
                                ],
                                'Subject' => "Subscription Reminder",
                                'TextPart' => "Subscription Reminder",
                                'HTMLPart' => $html,
                                'CustomID' => "AppGettingStartedTest"
                            ]
                        ]
                    ];
                    $this->mj->post(Resources::$Email, ['body' => $body]);
                }
                $user->expired = true;
                $user->save();
            }
        }
        
    }
}
