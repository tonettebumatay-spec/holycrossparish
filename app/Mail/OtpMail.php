<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $purpose;
    public $userName;

    public function __construct($otpCode, $purpose, $userName)
    {
        $this->otpCode = $otpCode;
        $this->purpose = $purpose;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->purpose) {
            'login' => 'Your Login OTP Code',
            'register' => 'Verify Your Email',
            'password_reset' => 'Reset Your Password',
            'admin_login' => 'Admin Login OTP Code',
            default => 'Your OTP Code',
        };

        return new Envelope(
            subject: $subject . ' - Holy Cross Parish',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}