<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        // Mail is sent explicitly in CandidateReview via ApplicationStatusChanged (supports templates).
        // Only persist to the database here for the in-app notification bell.
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->application->recruitment_stage->label();

        return (new MailMessage)
                    ->subject('Application Status Update')
                    ->line('Hello ' . $this->application->candidate->name . ',')
                    ->line('Your application for the ' . $this->application->job->title . ' position has been updated.')
                    ->line('Current Status: **' . $statusLabel . '**')
                    ->action('View Application', url('/portal/applications'))
                    ->line('Thank you for using our portal.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->job->title,
            'status' => $this->application->recruitment_stage->value,
            'message' => 'Your application status for ' . $this->application->job->title . ' changed to ' . $this->application->recruitment_stage->label(),
            'type' => 'status_update'
        ];
    }
}
