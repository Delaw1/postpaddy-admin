<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    public $subject;
    public $view;
    /**
     * Create a new message instance. 
     *
     * @return void
     */
    public function __construct($subject, $details, $view)
    {
        $this->details = $details;
        $this->subject = $subject;
        $this->view = $view; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view($this->view);
    }
}
