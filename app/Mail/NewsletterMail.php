<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public ?string $recipientEmail = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject ?: $this->newsletter->title,
            from: config('mail.from.address', 'noreply@sloughsend.org.uk'),
            replyTo: [$this->newsletter->reply_to ?? 'hello@sloughsend.org.uk'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'newsletter'    => $this->newsletter,
                'recipient'     => $this->recipientEmail,

                // ADD THESE LINES:
                'title'         => $this->newsletter->title,
                'subject'       => $this->newsletter->subject,
                'blocks'        => $this->newsletter->blocks ?? [],
                'style'         => $this->newsletter->style_config ?? [],
            ],
        );
    }
}
