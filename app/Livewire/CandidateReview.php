<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\Job;
use App\Notifications\ApplicationStatusChanged;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CandidateReview extends Component
{
    public Job $job;

    public Application $application;

    public string $notes = '';

    public function mount(Job $job, Application $application): void
    {
        $user = auth()->user();

        abort_unless($user->canAccessRecruitment(), 403);

        abort_unless($application->job_id === $job->id, 404);

        $this->job = $job;
        $this->application = $application->load(['candidate.profile', 'candidate.education', 'candidate.experiences', 'candidate.organizations', 'job', 'stageLogs.decidedBy']);
    }

    public function advance(): void
    {
        $this->validate([
            'notes' => ['nullable', 'string', 'min:5', 'max:2000'],
        ]);

        abort_unless($this->application->canAdvance(), 422);

        $nextStage = $this->application->nextStage();

        abort_if($nextStage === null, 422);

        ApplicationStageLog::create([
            'application_id' => $this->application->id,
            'stage' => $this->application->recruitment_stage->value,
            'decision' => 'passed',
            'notes' => $this->notes,
            'decided_by' => auth()->id(),
        ]);

        $this->application->update([
            'recruitment_stage' => $nextStage,
            'stage_updated_at' => now(),
        ]);

        try {
            $this->application->candidate->notify(
                new ApplicationStatusChanged($this->application->fresh(['job']))
            );
        } catch (\Throwable) {
            // Notification failure should not block the decision
        }

        $this->notes = '';
        $this->application->refresh()->load(['candidate.profile', 'candidate.education', 'candidate.experiences', 'candidate.organizations', 'job', 'stageLogs.decidedBy']);

        $this->dispatch('notify', message: __('Candidate advanced to') . ' ' . $this->application->recruitment_stage->label() . '.', type: 'success');
    }

    public function reject(): void
    {
        $this->validate([
            'notes' => ['nullable', 'string', 'min:5', 'max:2000'],
        ]);

        abort_unless($this->application->canAdvance(), 422);

        ApplicationStageLog::create([
            'application_id' => $this->application->id,
            'stage' => $this->application->recruitment_stage->value,
            'decision' => 'rejected',
            'notes' => $this->notes,
            'decided_by' => auth()->id(),
        ]);

        $this->application->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'stage_updated_at' => now(),
        ]);

        $this->notes = '';
        $this->application->refresh()->load(['candidate.profile', 'candidate.education', 'candidate.experiences', 'candidate.organizations', 'job', 'stageLogs.decidedBy']);

        $this->dispatch('notify', message: __('Candidate marked as Not Selected.'), type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.candidate-review');
    }
}
