<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdatedNotification extends Notification
{
    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        // Mail is sent by ApplicationStatusChanged so custom email templates stay supported.
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->application->recruitment_stage->notificationLabel();

        return (new MailMessage)
            ->subject('Pembaruan Status Lamaran')
            ->greeting('Yth. '.$this->application->candidate->name.',')
            ->line('Status lamaran Anda untuk posisi '.$this->application->job->title.' telah diperbarui.')
            ->line('Status saat ini: **'.$statusLabel.'**')
            ->action('Lihat Lamaran Saya', url(route('candidate.applications')))
            ->line('Terima kasih telah menggunakan portal rekrutmen kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->job->title,
            'status' => $this->application->recruitment_stage->value,
            'message' => 'Status lamaran Anda untuk '.$this->application->job->title.' berubah menjadi '.$this->application->recruitment_stage->notificationLabel().'.',
            'type' => 'status_update',
        ];
    }
}
