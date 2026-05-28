<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification
{
    public function __construct(public readonly Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $stage = $this->application->recruitment_stage;
        $stageLabel = $stage->notificationLabel();

        $jobLevel = is_object($job->level ?? null)
            ? ($job->level->value ?? 'staff')
            : (string) ($job->level ?? 'staff');

        $template = EmailTemplate::where('stage', $stage->value)
            ->where('job_level', $jobLevel)
            ->first()
            ?? EmailTemplate::where('stage', $stage->value)->first();

        $templateData = $template
            ? ['subject' => $template->subject, 'body' => $template->body]
            : EmailTemplate::defaultFor($stage);

        $replacements = [
            '{name}' => $notifiable->name,
            '{job}' => $job->title,
            '{status}' => $stageLabel,
            '{stage}' => $stageLabel,
        ];

        return (new MailMessage)
            ->subject(strtr($templateData['subject'], $replacements))
            ->greeting('Yth. '.$notifiable->name.',')
            ->line(strtr($templateData['body'], $replacements))
            ->action('Lihat Lamaran Saya', url(route('candidate.applications')));
    }
}
