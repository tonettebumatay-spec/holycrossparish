<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;
    public $reason;

    public function __construct(Certificate $certificate, $reason)
    {
        $this->certificate = $certificate;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificate Request Cancelled - Holy Cross Parish',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-cancelled',
        );
    }
}