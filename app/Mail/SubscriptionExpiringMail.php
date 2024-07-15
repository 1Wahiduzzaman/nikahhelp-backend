<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $user;

    public $domain;

    public $team;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($team, $user, $domain)
    {
        $this->team = $team;
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
        return $this->subject('Mail from MatrimonyAssist | Subscription expiring soon!')
            ->markdown('emails.subscription.expiring_subscription')->sendgrid([
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
