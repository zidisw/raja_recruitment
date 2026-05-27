<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\ApplicationStageLog;

class ApplicationService
{
    /**
     * Bulk reject applications and log the stage transitions.
     *
     * @param  array<int>  $applicationIds
     */
    public function bulkReject(array $applicationIds, int $decidedBy, string $notes): void
    {
        if (empty($applicationIds)) {
            return;
        }

        $now = now();

        $applications = Application::whereIn('id', $applicationIds)->get(['id', 'recruitment_stage']);

        if ($applications->isEmpty()) {
            return;
        }

        $logsData = $applications->map(fn ($app) => [
            'application_id' => $app->id,
            'stage' => $app->recruitment_stage instanceof RecruitmentStage ? $app->recruitment_stage->value : $app->recruitment_stage,
            'decision' => 'rejected',
            'notes' => $notes,
            'decided_by' => $decidedBy,
            'created_at' => $now,
        ])->all();

        // Batch insert logs in chunks of 500
        foreach (array_chunk($logsData, 500) as $chunk) {
            ApplicationStageLog::insert($chunk);
        }

        // Batch update applications
        Application::whereIn('id', $applicationIds)->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'status' => RecruitmentStage::REJECTED->toInt(),
            'stage_updated_at' => $now,
        ]);
    }
}
