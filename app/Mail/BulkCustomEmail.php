<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BulkCustomEmail extends Mailable
{
    public string $resolvedSubject;

    public string $resolvedBody;

    public function __construct(
        private readonly string $subject,
        private readonly string $body,
        private readonly string $candidateName,
        private readonly string $jobTitle,
    ) {
        $this->resolvedSubject = str_replace(
            ['{name}', '{job}'],
            [$this->candidateName, $this->jobTitle],
            $this->subject
        );

        $this->resolvedBody = str_replace(
            ['{name}', '{job}'],
            [$this->candidateName, $this->jobTitle],
            $this->body
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->resolvedSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bulk-custom-email',
            with: [
                'body'          => $this->resolvedBody,
                'candidateName' => $this->candidateName,
                'jobTitle'      => $this->jobTitle,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
