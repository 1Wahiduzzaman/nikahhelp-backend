<?php

namespace App\Mail;

use App\Enums\HttpStatusCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $user;

    public $token;
    //public $domain = HttpStatusCode::WEB_DOMAIN;

    /**
     * ForgetPasswordMail constructor.
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
        return $this->view('emails.forgetPassword')->with('user_name', $this->user->full_name)->sendgrid([
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $this->user->email, 'name' => $this->user->full_name],
                    ]
                ],
            ],
            'categories' => ['user_group1'],
    ]);
    }
}
