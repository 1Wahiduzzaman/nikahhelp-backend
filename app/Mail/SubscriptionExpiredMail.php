<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class SubscriptionExpiredMail extends Mailable
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
        return $this->subject('Mail from MatrimonyAssist | Subscription has been expired!')
            ->markdown('emails.subscription.expired_subscription')->with('user_name', $this->user->full_name)->sendgrid([
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
