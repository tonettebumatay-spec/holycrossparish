<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;
    public $requirements;

    public function __construct(Certificate $certificate, $requirements)
    {
        $this->certificate = $certificate;
        $this->requirements = $requirements;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificate Ready for Pickup - Holy Cross Parish',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-completed',
        );
    }
}