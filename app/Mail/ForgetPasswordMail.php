<?php

namespace App\Mail;

use App\Enums\HttpStatusCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    //public $domain = HttpStatusCode::WEB_DOMAIN;

    /**
     * ForgetPasswordMail constructor.
     * @param $user
     * @param $token
     * @param $domain
     */    
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.forgetPassword');
    }
}
