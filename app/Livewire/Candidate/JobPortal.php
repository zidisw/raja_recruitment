<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\Department;
use App\Models\Job;
use App\Models\Site;
use App\Models\User;
use App\Notifications\ApplicationReceived;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class JobPortal extends Component
{
    use WithPagination;

    public string $search = '';

    public string $department_filter = '';

    public string $site_filter = '';

    public string $level_filter = '';

    public bool $show_applied_only = false;

    public ?int $trackingJobId = null;

    public bool $showTrackingModal = false;

    public ?int $confirmingJobId = null;

    public ?Job $confirmingJob = null;

    public bool $showConfirmModal = false;

    public function mount(): void
    {
        abort_unless($this->currentUser()->hasUserRole(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSiteFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLevelFilter(): void
    {
        $this->resetPage();
    }

    public function updatingShowAppliedOnly(): void
    {
        $this->resetPage();
    }

    public function apply(Job $job): void
    {
        $user = $this->currentUser();

        abort_unless($job->is_active, 403);

        $alreadyApplied = Application::where('user_id', '=', $user->id, 'and')
            ->where('job_id', '=', $job->id, 'and')
            ->exists();

        if ($alreadyApplied) {
            $this->dispatch('notify', ['message' => __('You have already applied for this position.'), 'type' => 'error']);

            return;
        }

        $activeApplicationsCount = Application::where('user_id', '=', $user->id, 'and')
            ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED, RecruitmentStage::HIRED], 'and')
            ->count('*');

        if ($activeApplicationsCount >= 2) {
            $this->dispatch('notify', ['message' => __('You can only have 2 active applications at a time.'), 'type' => 'error']);

            return;
        }

        // Instead of applying directly, open the confirmation modal
        $this->confirmingJobId = $job->id;
        $this->confirmingJob = $job;
        $this->showConfirmModal = true;
    }

    public function confirmApply(): void
    {
        if (! $this->confirmingJobId || ! $this->confirmingJob) {
            return;
        }

        $user = $this->currentUser();

        $alreadyApplied = Application::where('user_id', $user->id)
            ->where('job_id', $this->confirmingJobId)
            ->exists();

        if ($alreadyApplied) {
            $this->dispatch('notify', ['message' => __('You have already applied for this position.'), 'type' => 'error']);
            $this->showConfirmModal = false;

            return;
        }

        $activeApplicationsCount = Application::where('user_id', $user->id)
            ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED, RecruitmentStage::HIRED])
            ->count();

        if ($activeApplicationsCount >= 2) {
            $this->dispatch('notify', ['message' => __('You can only have 2 active applications at a time.'), 'type' => 'error']);
            $this->showConfirmModal = false;

            return;
        }

        try {
            $application = Application::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'job_id' => $this->confirmingJobId,
                ],
                [
                    'recruitment_stage' => RecruitmentStage::APPLIED,
                ]
            );
        } catch (QueryException) {
            $this->dispatch('notify', ['message' => __('You have already applied for this position.'), 'type' => 'error']);
            $this->showConfirmModal = false;

            return;
        }

        if (! $application->wasRecentlyCreated) {
            $this->dispatch('notify', ['message' => __('You have already applied for this position.'), 'type' => 'error']);
            $this->showConfirmModal = false;

            return;
        }

        try {
            $user->notify(new ApplicationReceived($application->load('job')));
        } catch (\Throwable) {
            // Notification failure should not block the application
        }

        $this->showConfirmModal = false;
        $this->confirmingJobId = null;
        $this->confirmingJob = null;

        $this->dispatch('notify', ['message' => __('Application submitted successfully!'), 'type' => 'success']);
    }

    public function openTracking(int $jobId): void
    {
        $this->trackingJobId = $jobId;
        $this->showTrackingModal = true;
    }

    public function render(): \Illuminate\View\View
    {
        $user = $this->currentUser();

        $appliedJobIds = Application::where('user_id', $user->id)->pluck('job_id')->all();

        $query = Job::with(['department', 'site'])
            ->where('is_active', true)
            ->latest();

        if ($this->show_applied_only) {
            $query->whereIn('id', $appliedJobIds);
        }

        if ($this->search) {
            $query->whereAny(['title', 'description'], 'like', "%{$this->search}%");
        }

        if ($this->department_filter) {
            $query->where('department_id', $this->department_filter);
        }

        if ($this->site_filter) {
            $query->where('site_id', $this->site_filter);
        }

        if ($this->level_filter) {
            $query->where('level', $this->level_filter);
        }

        $trackingApplication = null;
        if ($this->trackingJobId) {
            $trackingApplication = Application::with('stageLogs.decidedBy')
                ->where('user_id', $user->id)
                ->where('job_id', $this->trackingJobId)
                ->first();
        }

        return view('livewire.candidate.job-portal', [
            'jobs' => $query->paginate(10),
            'appliedJobIds' => $appliedJobIds,
            'departments' => Cache::remember('ref.departments', 300, fn () => Department::orderBy('name')->get()),
            'sites' => Cache::remember('ref.sites', 300, fn () => Site::orderBy('name')->get()),
            'trackingApplication' => $trackingApplication,
        ]);
    }

    private function currentUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}
