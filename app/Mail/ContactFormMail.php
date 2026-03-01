<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->formData['email']],
            subject: '[AgriFish Contact] ' . $this->formData['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'formData' => $this->formData,
            ],
        );
    }
}
