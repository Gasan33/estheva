<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Forget Password Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.forget'
        );
    }

    /**
     * Pass data to the email view.
     */
    public function build()
    {
        $data = $this->token;

        return $this->from('support@estheva-clinic.com')->view('mail.forget', compact('data'))->subject('Password Reset Link');
        // ->with(['data' => $this->token]);

        // return $this->view('mail.forget')
        //     ->with(['data' => $this->token]); 
    }
}
