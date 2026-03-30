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
use Illuminate\Support\Facades\Cache;
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

    public array $selectedIds = [];
    
    public bool $selectAll = false;

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
        $this->resetSelection();
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

        $oldStage = $application->recruitment_stage->value;

        $application->update([
            'recruitment_stage' => RecruitmentStage::HR_INTERVIEW,
            'stage_updated_at' => now(),
        ]);

        \App\Models\ApplicationStageLog::create([
            'application_id' => $application->id,
            'stage' => $oldStage,
            'decision' => 'passed',
            'notes' => 'Lolos ke tahap HR Interview (Administrasi)',
            'decided_by' => Auth::id(),
        ]);

        $this->dispatch('notify', ['message' => __('Candidate passed administrative screening.'), 'type' => 'success']);
    }

    public function rejectApplication(int $applicationId): void
    {
        $application = Application::findOrFail($applicationId);

        if ($application->recruitment_stage->isTerminal() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat mengubah status untuk kandidat yang sudah berada di log terminal (Rejected/Hired).');
        }

        $oldStage = $application->recruitment_stage->value;

        $application->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'stage_updated_at' => now(),
        ]);

        \App\Models\ApplicationStageLog::create([
            'application_id' => $application->id,
            'stage' => $oldStage,
            'decision' => 'rejected',
            'notes' => 'Ditolak secara langsung (Administrasi/Riwayat)',
            'decided_by' => Auth::id(),
        ]);

        $this->dispatch('notify', ['message' => __('Candidate rejected.'), 'type' => 'success']);
    }

    public function updateProgressStage(int $applicationId, string $stage): void
    {
        $application = Application::findOrFail($applicationId);
        
        if ($application->recruitment_stage->isTerminal() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat mengubah status untuk kandidat yang sudah berada di log terminal (Rejected/Hired).');
        }

        $oldStage = $application->recruitment_stage->value;
        $nextStage = RecruitmentStage::from($stage);

        $application->update([
            'recruitment_stage' => $nextStage,
            'stage_updated_at' => now(),
        ]);

        \App\Models\ApplicationStageLog::create([
            'application_id' => $application->id,
            'stage' => $oldStage,
            'decision' => $nextStage === RecruitmentStage::REJECTED ? 'rejected' : 'passed',
            'notes' => 'Status diubah secara manual via dropdown',
            'decided_by' => Auth::id(),
        ]);

        $this->dispatch('notify', ['message' => __('Recruitment stage updated.'), 'type' => 'success']);
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedTab(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function resetSelection(): void
    {
        $this->selectAll = false;
        $this->selectedIds = [];
    }

    public function bulkPassAdministrative(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        // Pre-fetch old stages so we log the correct stage they passed
        $applications = Application::whereIn('id', $this->selectedIds)->get(['id', 'recruitment_stage']);
        
        Application::whereIn('id', $this->selectedIds)->update([
            'recruitment_stage' => RecruitmentStage::HR_INTERVIEW,
            'stage_updated_at' => now(),
        ]);

        $logs = [];
        $now = now();
        $adminId = Auth::id();
        foreach ($applications as $app) {
            $logs[] = [
                'application_id' => $app->id,
                'stage' => $app->recruitment_stage->value,
                'decision' => 'passed',
                'notes' => 'Lolos masal ke tahap HR Interview',
                'decided_by' => $adminId,
                'created_at' => $now,
            ];
        }
        \App\Models\ApplicationStageLog::insert($logs);

        $this->dispatch('notify', ['message' => __('Kandidat yang dipilih berhasil diloloskan ke On Progress.'), 'type' => 'success']);
        $this->resetSelection();
    }

    public function bulkReject(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $applications = Application::whereIn('id', $this->selectedIds)->get(['id', 'recruitment_stage']);

        Application::whereIn('id', $this->selectedIds)->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'stage_updated_at' => now(),
        ]);

        $logs = [];
        $now = now();
        $adminId = Auth::id();
        foreach ($applications as $app) {
            $logs[] = [
                'application_id' => $app->id,
                'stage' => $app->recruitment_stage->value,
                'decision' => 'rejected',
                'notes' => 'Ditolak masal',
                'decided_by' => $adminId,
                'created_at' => $now,
            ];
        }
        \App\Models\ApplicationStageLog::insert($logs);

        $this->dispatch('notify', ['message' => __('Kandidat yang dipilih berhasil ditolak.'), 'type' => 'success']);
        $this->resetSelection();
    }

    public function exportCsv()
    {
        $applications = $this->getFilteredQuery()->get();
        
        $csvData = "Nama Kandidat,Email,Posisi,Departemen,Site,Tanggal Lamar,Stage,Status Lolos Administrasi\n";
        
        foreach ($applications as $app) {
            $name = '"' . str_replace('"', '""', $app->candidate->name) . '"';
            $email = '"' . str_replace('"', '""', $app->candidate->email) . '"';
            $jobTitle = '"' . str_replace('"', '""', $app->job->title) . '"';
            $dept = '"' . str_replace('"', '""', $app->job->department?->name ?? '') . '"';
            $site = '"' . str_replace('"', '""', $app->job->site?->name ?? '') . '"';
            $appliedDate = $app->created_at->format('Y-m-d');
            $stage = $app->recruitment_stage->name;
            $passedAdmin = $app->stage_updated_at ? $app->stage_updated_at->format('Y-m-d') : '';
            
            $csvData .= "{$name},{$email},{$jobTitle},{$dept},{$site},{$appliedDate},{$stage},{$passedAdmin}\n";
        }
        
        $filename = "export_kandidat_{$this->tab}_" . now()->format('Ymd_His') . ".csv";
        
        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $filename);
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
        $this->dispatch('notify', ['message' => __('HR Interview scheduled successfully.'), 'type' => 'success']);
    }

    private function getFilteredQuery()
    {
        $query = Application::with([
            'candidate.profile',
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
            ]);
        }

        // On Progress: candidates actively progressing through the pipeline.
        if ($this->tab === 'on-progress') {
            $query->whereNotIn('recruitment_stage', [
                RecruitmentStage::APPLIED,
                RecruitmentStage::HIRED,
                RecruitmentStage::REJECTED,
            ])
            ->whereDoesntHave('interviews', function ($q) {
                $q->where('interview_type', 'HR Interview');
            });
        }
        
        return $query;
    }

    public function render(): \Illuminate\View\View
    {
        $query = $this->getFilteredQuery();

        return view('livewire.candidate-management', [
            'applications' => $query->paginate($this->perPage),
            'departments' => Cache::remember('ref.departments', 300, fn () => Department::query()->orderBy('name')->get(['id', 'name'])),
            'sites' => Cache::remember('ref.sites', 300, fn () => Site::query()->orderBy('name')->get(['id', 'name'])),
            'interviewers' => Cache::remember('ref.interviewers', 300, fn () => User::whereIn('role', [UserRole::Admin, UserRole::HR, UserRole::Interviewer])->get()),
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
