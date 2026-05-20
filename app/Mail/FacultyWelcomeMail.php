<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacultyWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $recipientEmail,
        public string $plainPassword,
        public string $role,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Faculty Elevate – Your Account is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.faculty_welcome',
        );
    }
}
