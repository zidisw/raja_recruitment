<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public $isForHR;

    public function __construct(Application $application, bool $isForHR = false)
    {
        $this->application = $application;
        $this->isForHR = $isForHR;
    }

    public function via(object $notifiable): array
    {
        // HR receives mail + in-app notification.
        // Candidate already receives a proper mail from ApplicationReceived in JobPortal.
        return $this->isForHR ? ['mail', 'database'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->isForHR) {
            return (new MailMessage)
                ->subject('New Job Application')
                ->line('A new application has been submitted for '.$this->application->job->title)
                ->action('Review Applications', url(route('applications.job', $this->application->job_id)))
                ->line('Please review it in the Recruitment Portal.');
        }

        return (new MailMessage)
            ->subject('Application Received: '.$this->application->job->title)
            ->line('Hello '.$this->application->candidate->name.',')
            ->line('Thank you for applying for the '.$this->application->job->title.' position.')
            ->line('We have successfully received your application. We will review it and get back to you soon.')
            ->action('View My Applications', url(route('candidate.applications')))
            ->line('Thank you for your interest in joining our team!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->job->title,
            'message' => $this->isForHR
                ? 'New application submitted by '.$this->application->candidate->name
                : 'Your application has been received!',
            'type' => 'new_application',
        ];
    }
}
