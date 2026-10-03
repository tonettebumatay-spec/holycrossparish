<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Appointment $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Has Expired - Holy Cross Parish',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-expired',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}