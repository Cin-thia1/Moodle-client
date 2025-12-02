<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $userEmail;

    public function __construct($data, $userEmail)
    {
        $this->data = $data;
        $this->userEmail = $userEmail;
    }

    public function build()
    {
        return $this->subject('New Contact Message')
                    ->view('emails.contact')
                    ->with([
                        'data' => $this->data,
                        'userEmail' => $this->userEmail,
                    ]);
    }
}
