<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class SubscriptionNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public $domain;

    public $team;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $team, $subscription, $domain)
    {
        $this->user = $user;
        $this->team = $team;
        $this->subscription = $subscription;
        $this->domain = $domain;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from MatrimoyAssist | Subscription Payment!')
            ->markdown('emails.subscription.new_subscription')->with([
                'team' => $this->team,
                'subscription' => $this->subscription,
                'domain' => $this->domain,
            ]);
    }
}
