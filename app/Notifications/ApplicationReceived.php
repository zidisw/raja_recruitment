<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;

        return (new MailMessage)
            ->subject(__('Application Received — :job', ['job' => $job->title]))
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
            ->line(__('Thank you for applying for the position of **:job** at PT. Roda Jaya Sakti.', ['job' => $job->title]))
            ->line(__('Your application has been received and is currently under review. We will notify you of any updates.'))
            ->action(__('Track Your Application'), url(route('candidate.applications')))
            ->line(__('Thank you for your interest in joining our team.'));
    }
}
