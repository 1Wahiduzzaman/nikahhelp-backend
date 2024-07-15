<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class UserDeletedMail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from MatrimonyAssist | Account Deleted!')
            ->markdown('emails.status.delete')->with('user_name', $this->user->full_name)->sendgrid([
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
