<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecruitmentEventNotification extends Notification
{
    /**
     * @param  array<int, string>  $channels
     */
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly ?string $actionUrl = null,
        public readonly string $type = 'recruitment_event',
        public readonly ?string $mailSubject = null,
        private readonly array $channels = ['database'],
    ) {}

    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->mailSubject ?? $this->title)
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
            ->line($this->message);

        if ($this->actionUrl) {
            $mail->action(__('Open Recruitment Portal'), $this->actionUrl);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'job_title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
        ];
    }
}
