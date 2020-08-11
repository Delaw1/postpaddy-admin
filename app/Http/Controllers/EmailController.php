<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendMail(Request $request)
    {
        $subject = "Geonel";
        $view = 'emails.geonel';
        $email =  $request->input('email_to');
        $mailBody = new MyMail($subject, $request->all(), $view);
        $mail = Mail::to($email)->send($mailBody);

        if ($mail) {
            return true;
        }
        return false;
    }
}
