<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Department;
use App\Models\Site;
use App\Models\Interview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CandidateManagement extends Component
{
    use WithPagination;

    public string $tab = 'administrasi';

    public string $search = '';

    public int $perPage = 10;

    public string $filterDepartment = '';

    public string $filterSite = '';

    public string $filterStage = '';

    public string $filterStatus = '';

    public ?int $expandedRow = null;

    // Interview Scheduling fields
    public bool $showScheduleModal = false;
    public ?int $schedulingApplicationId = null;
    public ?int $interviewer_id = null;
    public string $scheduled_date = '';
    public string $scheduled_time = '';
    public string $hr_notes = '';

    public function mount(string $tab = 'administrasi'): void
    {
        abort_unless(Auth::user()?->canAccessRecruitment(), 403);

        if (in_array($tab, ['administrasi', 'on-progress', 'riwayat'])) {
            $this->tab = $tab;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDepartment(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSite(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStage(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetDetailedFilters(): void
    {
        $this->search = '';
        $this->perPage = 10;
        $this->filterDepartment = '';
        $this->filterSite = '';
        $this->filterStage = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function passAdministrative(int $applicationId): void
    {
        $application = Application::findOrFail($applicationId);

        $application->update([
            'recruitment_stage' => RecruitmentStage::HR_INTERVIEW,
            'stage_updated_at' => now(),
        ]);

        $this->dispatch('notify', message: __('Candidate passed administrative screening.'), type: 'success');
    }

    public function rejectApplication(int $applicationId): void
    {
        $application = Application::findOrFail($applicationId);

        $application->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'stage_updated_at' => now(),
        ]);

        $this->dispatch('notify', message: __('Candidate rejected.'), type: 'success');
    }

    public function updateProgressStage(int $applicationId, string $stage): void
    {
        $application = Application::findOrFail($applicationId);
        $nextStage = RecruitmentStage::from($stage);

        $application->update([
            'recruitment_stage' => $nextStage,
            'stage_updated_at' => now(),
        ]);

        $this->dispatch('notify', message: __('Recruitment stage updated.'), type: 'success');
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function openScheduleInterview(int $applicationId): void
    {
        $this->schedulingApplicationId = $applicationId;
        $this->interviewer_id = null;
        $this->scheduled_date = '';
        $this->scheduled_time = '';
        $this->hr_notes = '';
        $this->showScheduleModal = true;
    }

    public function saveInterview(): void
    {
        $validated = $this->validate([
            'schedulingApplicationId' => ['required', 'exists:applications,id'],
            'interviewer_id' => ['required', 'exists:users,id'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'hr_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $scheduledAt = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);

        Interview::create([
            'application_id' => $validated['schedulingApplicationId'],
            'interviewer_id' => $validated['interviewer_id'],
            'interview_type' => 'HR Interview',
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
            'hr_notes' => $validated['hr_notes'],
        ]);

        $this->showScheduleModal = false;
        $this->dispatch('notify', message: __('HR Interview scheduled successfully.'), type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with([
            'candidate.profile',
            'candidate.education',
            'candidate.experiences',
            'candidate.organizations',
            'job.department',
            'job.site',
        ])
            ->latest('created_at');

        if ($this->search !== '') {
            $query->whereHas('candidate', function ($q): void {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterDepartment !== '') {
            $query->whereHas('job', fn ($q) => $q->where('department_id', (int) $this->filterDepartment));
        }

        if ($this->filterSite !== '') {
            $query->whereHas('job', fn ($q) => $q->where('site_id', (int) $this->filterSite));
        }

        if ($this->filterStage !== '') {
            $query->where('recruitment_stage', $this->filterStage);
        }

        if ($this->filterStatus !== '') {
            $query->where('recruitment_stage', $this->filterStatus);
        }

        // Administrasi: candidates still in admin screening (already applied).
        if ($this->tab === 'administrasi') {
            $query->whereIn('recruitment_stage', [
                RecruitmentStage::APPLIED,
                RecruitmentStage::ADMIN_REVIEW,
            ]);
        }

        // On Progress: candidates who passed administration and are still waiting for HR Interview scheduling.
        if ($this->tab === 'on-progress') {
            $query->where('recruitment_stage', RecruitmentStage::HR_INTERVIEW)
                ->whereDoesntHave('hrInterview');
        }

        return view('livewire.candidate-management', [
            'applications' => $query->paginate($this->perPage),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'sites' => Site::query()->orderBy('name')->get(['id', 'name']),
            'interviewers' => User::whereIn('role', [UserRole::Admin, UserRole::HR, UserRole::Interviewer])->get(),
            'progressStages' => [
                RecruitmentStage::HR_INTERVIEW,
                RecruitmentStage::USER_INTERVIEW,
                RecruitmentStage::OFFERING,
                RecruitmentStage::PSYCHOTEST,
                RecruitmentStage::MCU,
                RecruitmentStage::ONBOARDING,
            ],
            'allStages' => RecruitmentStage::cases(),
            'statusOptions' => RecruitmentStage::cases(),
        ]);
    }
}
