<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $email;

    public $firstname;

    public $lastname;

    public $telephone;

    public $message;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->message = $data['message'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->telephone = $data['telephone'];
        $this->email = $data['email'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact')->with('data', $this->data)->sendgrid([
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $this->data['email'], 'name' => $this->data['first_name'].' '. $this->data['last_name'],
                    ]
                ],
            ],
            'categories' => ['user_group1'],
    ]);
    }
}
