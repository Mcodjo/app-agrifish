<?php

namespace App\Mail;

use App\Models\Signature;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VerifySignature extends Mailable
{
    public function __construct(public Signature $signature, public string $url)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmez votre signature : '.config('petition.title'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify');
    }
}
