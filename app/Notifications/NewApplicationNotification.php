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
                ->subject('Lamaran Baru - '.$this->application->job->title)
                ->line('Terdapat lamaran baru untuk posisi '.$this->application->job->title.'.')
                ->line('Kandidat: '.$this->application->candidate->name)
                ->action('Tinjau Lamaran', url(route('candidates.administrasi')))
                ->line('Silakan tinjau lamaran tersebut melalui portal rekrutmen.');
        }

        return (new MailMessage)
            ->subject('Lamaran Diterima - '.$this->application->job->title)
            ->greeting('Yth. '.$this->application->candidate->name.',')
            ->line('Terima kasih telah melamar untuk posisi '.$this->application->job->title.'.')
            ->line('Lamaran Anda telah kami terima dan akan ditinjau oleh Tim Rekrutmen.')
            ->action('Lihat Lamaran Saya', url(route('candidate.applications')))
            ->line('Terima kasih atas minat Anda untuk bergabung dengan PT Roda Jaya Sakti.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->job->title,
            'message' => $this->isForHR
                ? 'Lamaran baru diterima dari '.$this->application->candidate->name
                : 'Lamaran Anda telah diterima.',
            'type' => 'new_application',
        ];
    }
}
