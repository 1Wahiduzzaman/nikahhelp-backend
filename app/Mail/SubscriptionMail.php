<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $domain;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $domain)
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
        return $this->subject('Mail from Matrimonial-assist')
            ->markdown('emails.subscription.expiring_subscription');
    }
}
