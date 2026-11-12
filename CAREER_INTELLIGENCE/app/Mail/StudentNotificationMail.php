<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageBody;

    public string $subjectLine;

    public ?string $studentName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectLine, string $messageBody, ?string $studentName = null)
    {
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
        $this->studentName = $studentName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $admin = auth()->user();
        $senderEmail = config('mail.from.address', 'natiakifani@gmail.com');
        $senderName = config('mail.from.name', 'Administrator Career Intelligence');
        if ($admin && $admin->name) {
            $senderName = $admin->name.' - Career Intelligence';
        }

        $replyTo = [];
        if ($admin && filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address($admin->email, $admin->name ?? 'Administrator');
        }

        return new Envelope(
            from: new Address($senderEmail, $senderName),
            replyTo: $replyTo,
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
