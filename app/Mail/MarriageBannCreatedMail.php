<?php

namespace App\Mail;

use App\Models\MarriageBann;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarriageBannCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bann;

    public function __construct(MarriageBann $bann)
    {
        $this->bann = $bann;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Marriage Banns Posted - Holy Cross Parish',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.marriage-bann-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}