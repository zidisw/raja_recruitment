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

        // Try to use a custom email template for this stage and job level
        $jobLevel = $job->level->value ?? 'staff'; // e.g. 'staff' or 'non_staff'
        $template = EmailTemplate::where('stage', $stage->value)
            ->where('job_level', $jobLevel)
            ->first()
            ?? EmailTemplate::where('stage', $stage->value)->first(); // fallback to any level

        if ($template) {
            $body = str_replace(
                ['{name}', '{job}', '{status}', '{stage}'],
                [$notifiable->name, $job->title, $stage->label(), $stage->label()],
                $template->body
            );

            return (new MailMessage)
                ->subject($template->subject)
                ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
                ->line($body)
                ->action(__('Track Your Application'), url(route('candidate.applications')));
        }

        // Default template if no custom template exists
        return (new MailMessage)
            ->subject(__('Application Update — :job', ['job' => $job->title]))
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
            ->line(__('Your application for **:job** has been updated.', ['job' => $job->title]))
            ->line(__('Current status: **:status**', ['status' => $stage->label()]))
            ->action(__('Track Your Application'), url(route('candidate.applications')))
            ->line(__('Thank you for your patience.'));
    }
}
