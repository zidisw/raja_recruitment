<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\RecruitmentEventNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RecruitmentNotificationService
{
    public function notifyStageChanged(Application $application): void
    {
        $application->loadMissing(['candidate', 'job']);

        if (! $application->candidate) {
            return;
        }

        $this->send($application->candidate, new ApplicationStatusUpdatedNotification($application));
        $this->send($application->candidate, new ApplicationStatusChanged($application));
    }

    public function notifyDatabaseAndMail(
        User|Collection|array $notifiables,
        string $title,
        string $message,
        ?string $actionUrl = null,
        string $type = 'recruitment_event',
        ?string $mailSubject = null,
    ): void {
        foreach ($this->normalizeNotifiables($notifiables) as $notifiable) {
            $this->send($notifiable, new RecruitmentEventNotification(
                title: $title,
                message: $message,
                actionUrl: $actionUrl,
                type: $type,
                mailSubject: $mailSubject,
                channels: ['database'],
            ));

            $this->send($notifiable, new RecruitmentEventNotification(
                title: $title,
                message: $message,
                actionUrl: $actionUrl,
                type: $type,
                mailSubject: $mailSubject,
                channels: ['mail'],
            ));
        }
    }

    public function recruitmentManagers(?int $departmentId = null): Collection
    {
        $query = User::query()->whereIn('role', [
            UserRole::SuperAdmin,
            UserRole::Admin,
            UserRole::HR,
            UserRole::Interviewer,
        ]);

        if ($departmentId) {
            $query->where(function ($q) use ($departmentId): void {
                $q->whereIn('role', [UserRole::SuperAdmin, UserRole::Admin])
                    ->orWhere(function ($legacy) use ($departmentId): void {
                        $legacy->whereIn('role', [UserRole::HR, UserRole::Interviewer])
                            ->where('department_id', $departmentId);
                    });
            });
        }

        return $query->get();
    }

    private function send(User $notifiable, Notification $notification): void
    {
        try {
            $notifiable->notify($notification);
        } catch (\Throwable $exception) {
            Log::warning('Recruitment notification failed.', [
                'notifiable_id' => $notifiable->id,
                'notification' => $notification::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @return iterable<int, User>
     */
    private function normalizeNotifiables(User|Collection|array $notifiables): iterable
    {
        if ($notifiables instanceof User) {
            return [$notifiables];
        }

        return $notifiables;
    }
}
