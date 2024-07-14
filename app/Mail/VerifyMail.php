<?php

namespace App\Mail;
use App\Enums\HttpStatusCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $domain;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$domain)
    {
        $this->user = $user;
        $this->domain = $domain;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verifyUser')->with('user_name', $this->user->full_name);
    }
}
