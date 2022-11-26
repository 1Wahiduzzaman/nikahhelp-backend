<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $firstname;
    public $lastname;
    public $telephone;
    public $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
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
        return $this->view('emails.contact')->with('data', $this->data);
    }
}
