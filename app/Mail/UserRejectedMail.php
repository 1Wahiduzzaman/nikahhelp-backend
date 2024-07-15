<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class UserRejectedMail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $user;

    public $rejected_notes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $rejected_notes)
    {
        //
        $this->user = $user;
        $this->rejected_notes = $rejected_notes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from MatrimonyAssist | Verification Rejected!')
            ->markdown('emails.status.reject')->with('user_name', $this->user->full_name)->sendgrid([
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
