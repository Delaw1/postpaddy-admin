<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;
use \Mailjet\Resources;

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
                        'Email' => "info@digifigs.com",
                        'Name' => "Postlate"
                    ],
                    'To' => [
                        [
                            'Email' => $data['email'],
                            'Name' => $data['name']
                        ]
                    ],
                    'Subject' => "Subscription successfully",
                    'TextPart' => "Subscription successfully",
                    'HTMLPart' => $html,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $this->mj->post(Resources::$Email, ['body' => $body]);
    }

    public function sendVerificationEmail(array $data)
    {
        $html = file_get_contents(resource_path('views/emails/welcomemail.blade.php'));
        $html = str_replace(
            ['{{NAME}}', '{{VERIFY_LINK}}'],
            [$data['name'], "https://postslate.com/api/VerifyEmail/" . base64_encode($data['email'])],
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
                            'Email' => $data['email'],
                            'Name' => $data['name']
                        ]
                    ],
                    'Subject' => "Welcome to Postslate",
                    'TextPart' => "Welcome to Postslate",
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
            ['{{TOKEN}}'],
            [$token],
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
                            'Email' => $email,
                        ]
                    ],
                    'Subject' => "Postlate Password Reset",
                    'TextPart' => "Postlate Password Reset",
                    'HTMLPart' => $html,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $this->mj->post(Resources::$Email, ['body' => $body]);
    }
}
