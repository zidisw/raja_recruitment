<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\User;
use App\Enums\UserRole;
use App\Notifications\NewApplicationNotification;
use App\Notifications\ApplicationStatusUpdatedNotification;

class ApplicationObserver
{
    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        // Seed the history timeline from APPLIED so stage history always starts from submission.
        ApplicationStageLog::create([
            'application_id' => $application->id,
            'stage' => $application->recruitment_stage->value,
            'decision' => 'passed',
            'notes' => 'Lamaran masuk (Applied)',
            'decided_by' => $application->user_id,
        ]);

        // Notify managers when a new application is created.
        $deptId = $application->job?->department_id;
        $managerQuery = User::whereIn('role', [
            UserRole::SuperAdmin,
            UserRole::Admin,
            UserRole::HR,
            UserRole::Interviewer,
        ]);

        // Keep backward compatibility: only filter by department for legacy scoped roles.
        if ($deptId) {
            $managerQuery->where(function ($q) use ($deptId): void {
                $q->whereIn('role', [UserRole::SuperAdmin, UserRole::Admin])
                    ->orWhere(function ($legacy) use ($deptId): void {
                        $legacy->whereIn('role', [UserRole::HR, UserRole::Interviewer])
                            ->where('department_id', $deptId);
                    });
            });
        }

        foreach ($managerQuery->get() as $manager) {
            $manager->notify(new NewApplicationNotification($application, true));
        }
    }

    /**
     * Handle the Application "updated" event.
     */
    public function updated(Application $application): void
    {
        if ($application->wasChanged('recruitment_stage')) {
            if ($application->candidate) {
                $application->candidate->notify(new ApplicationStatusUpdatedNotification($application));
            }
        }
    }

    /**
     * Handle the Application "deleted" event.
     */
    public function deleted(Application $application): void
    {
        //
    }
}
