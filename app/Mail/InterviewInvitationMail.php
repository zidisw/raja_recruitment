<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $roleType;

    /**
     * Create a new message instance.
     */
    public function __construct(Interview $interview, string $roleType)
    {
        $this->interview = $interview;
        $this->roleType = $roleType; // 'candidate' or 'interviewer'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Wawancara - ' . $this->interview->application->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.interview-invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
