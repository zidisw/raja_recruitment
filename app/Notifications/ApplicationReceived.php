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
            ->subject('Lamaran Diterima - '.$job->title)
            ->greeting('Yth. '.$notifiable->name.',')
            ->line('Terima kasih telah melamar untuk posisi **'.$job->title.'** di PT Roda Jaya Sakti.')
            ->line('Lamaran Anda telah kami terima dan akan ditinjau oleh Tim Rekrutmen.')
            ->line('Kami akan mengirimkan pemberitahuan apabila terdapat pembaruan pada proses seleksi Anda.')
            ->action('Lihat Lamaran Saya', url(route('candidate.applications')))
            ->line('Terima kasih atas minat Anda untuk bergabung dengan PT Roda Jaya Sakti.');
    }
}
