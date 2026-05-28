<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Mcu;
use App\Models\OfferingLetter;
use App\Models\Onboarding;
use App\Models\Psychotest;
use App\Models\Site;
use App\Models\User;
use App\Services\RecruitmentNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    public bool $selectionMode = false;

    public bool $showBulkStageModal = false;

    public string $bulkStage = '';

    // Interview Scheduling fields
    public bool $showScheduleModal = false;

    public ?int $schedulingApplicationId = null;

    public ?int $interviewer_id = null;

    public string $scheduled_date = '';

    public string $scheduled_time = '';

    public string $hr_notes = '';

    public function mount(string $tab = 'administrasi'): void
    {
        abort_unless($this->currentUser()->canAccessRecruitment(), 403);

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

        $oldStage = $application->recruitment_stage;

        if ($oldStage !== RecruitmentStage::ADMINISTRASI) {
            $application->update([
                'recruitment_stage' => RecruitmentStage::ADMINISTRASI,
                'stage_updated_at' => now(),
            ]);

            \App\Models\ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage->value,
                'decision' => 'passed',
                'notes' => 'Lolos administrasi',
                'decided_by' => Auth::id(),
            ]);
        }

        $this->dispatch('notify', ['message' => __('Candidate passed administrative screening.'), 'type' => 'success']);
    }

    public function rejectApplication(int $applicationId): void
    {
        $application = Application::findOrFail($applicationId);

        if ($application->recruitment_stage->isTerminal() && ! $this->currentUser()->isSuperAdmin()) {
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

    public function deleteApplication(int $applicationId): void
    {
        if (! $this->currentUser()->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat menghapus data kandidat dari riwayat.');
        }

        $application = Application::with(['stageLogs', 'interviews', 'offeringLetter', 'psychotest', 'mcu', 'onboarding'])
            ->findOrFail($applicationId);

        DB::transaction(function () use ($application): void {
            $application->stageLogs()->delete();
            $application->interviews()->delete();
            $application->offeringLetter?->delete();
            $application->psychotest?->delete();
            $application->mcu?->delete();
            $application->onboarding?->delete();
            $application->delete();
        });

        $this->resetSelection();
        $this->dispatch('notify', ['message' => __('Riwayat kandidat berhasil dihapus.'), 'type' => 'success']);
    }

    public function updateProgressStage(int $applicationId, string $stage): void
    {
        $application = Application::findOrFail($applicationId);

        if ($application->recruitment_stage->isTerminal() && ! $this->currentUser()->isSuperAdmin()) {
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
            $this->selectedIds = $this->getFilteredQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedTab(): void
    {
        $this->resetPage();
        $this->resetSelection();
        $this->selectionMode = false;
    }

    public function resetSelection(): void
    {
        $this->selectAll = false;
        $this->selectedIds = [];
    }

    public function toggleSelectionMode(): void
    {
        $this->selectionMode = ! $this->selectionMode;

        if (! $this->selectionMode) {
            $this->resetSelection();
        }
    }

    public function openBulkStageModal(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', ['message' => __('Pilih minimal 1 kandidat terlebih dahulu.'), 'type' => 'error']);

            return;
        }

        $this->bulkStage = '';
        $this->showBulkStageModal = true;
    }

    private function bulkStageAllowedValues(): array
    {
        if ($this->tab === 'on-progress') {
            return [
                RecruitmentStage::ADMINISTRASI->value,
                RecruitmentStage::HR_INTERVIEW->value,
                RecruitmentStage::USER_INTERVIEW->value,
                RecruitmentStage::OFFERING->value,
                RecruitmentStage::PSYCHOTEST->value,
                RecruitmentStage::MCU->value,
                RecruitmentStage::ONBOARDING->value,
                RecruitmentStage::HIRED->value,
                RecruitmentStage::REJECTED->value,
            ];
        }

        return array_map(static fn (RecruitmentStage $s) => $s->value, RecruitmentStage::cases());
    }

    public function bulkUpdateStage(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $this->validate([
            'bulkStage' => ['required', Rule::in($this->bulkStageAllowedValues())],
        ]);

        $applications = Application::whereIn('id', $this->selectedIds)->get(['id', 'recruitment_stage']);

        if (! $this->currentUser()->isSuperAdmin() && $applications->contains(fn (Application $a) => $a->recruitment_stage->isTerminal())) {
            $this->dispatch('notify', ['message' => __('Sebagian kandidat terpilih sudah berada di status terminal (Rejected/Hired). Hanya superadmin yang dapat mengubahnya.'), 'type' => 'error']);

            return;
        }

        $nextStage = RecruitmentStage::from($this->bulkStage);

        $logs = [];
        $now = now();
        $adminId = Auth::id();
        foreach ($applications as $app) {
            $oldStage = $app->recruitment_stage;

            $app->update([
                'recruitment_stage' => $nextStage,
                'stage_updated_at' => $now,
            ]);

            $logs[] = [
                'application_id' => $app->id,
                'stage' => $oldStage->value,
                'decision' => $nextStage === RecruitmentStage::REJECTED ? 'rejected' : 'passed',
                'notes' => 'Status diubah secara massal via bulk action',
                'decided_by' => $adminId,
                'created_at' => $now,
            ];
        }
        ApplicationStageLog::insert($logs);

        $this->showBulkStageModal = false;
        $this->bulkStage = '';
        $this->dispatch('notify', ['message' => __('Tahapan berhasil diperbarui untuk :count kandidat.', ['count' => $applications->count()]), 'type' => 'success']);
        $this->resetSelection();
    }

    public function bulkPassAdministrative(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        // Pre-fetch old stages so we log the correct stage they passed
        $applications = Application::whereIn('id', $this->selectedIds)->get(['id', 'recruitment_stage']);

        $logs = [];
        $now = now();
        $adminId = Auth::id();
        foreach ($applications as $app) {
            $oldStage = $app->recruitment_stage;

            $app->update([
                'recruitment_stage' => RecruitmentStage::ADMINISTRASI,
                'stage_updated_at' => $now,
            ]);

            $logs[] = [
                'application_id' => $app->id,
                'stage' => $oldStage->value,
                'decision' => 'passed',
                'notes' => 'Lolos administrasi (massal)',
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

        if (! $this->currentUser()->isSuperAdmin() && $applications->contains(fn (Application $a) => $a->recruitment_stage->isTerminal())) {
            $this->dispatch('notify', ['message' => __('Sebagian kandidat terpilih sudah berada di status terminal (Rejected/Hired). Hanya superadmin yang dapat mengubahnya.'), 'type' => 'error']);

            return;
        }

        $logs = [];
        $now = now();
        $adminId = Auth::id();
        foreach ($applications as $app) {
            $oldStage = $app->recruitment_stage;

            $app->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => $now,
            ]);

            $logs[] = [
                'application_id' => $app->id,
                'stage' => $oldStage->value,
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

    public function bulkDeleteApplications(): void
    {
        if (! $this->currentUser()->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat menghapus data kandidat dari riwayat.');
        }

        if (empty($this->selectedIds)) {
            return;
        }

        $ids = $this->selectedIds;

        DB::transaction(function () use ($ids): void {
            ApplicationStageLog::whereIn('application_id', $ids)->delete();
            Interview::whereIn('application_id', $ids)->delete();
            OfferingLetter::whereIn('application_id', $ids)->delete();
            Psychotest::whereIn('application_id', $ids)->delete();
            Mcu::whereIn('application_id', $ids)->delete();
            Onboarding::whereIn('application_id', $ids)->delete();
            Application::whereIn('id', $ids)->delete();
        });

        $deletedCount = count($ids);
        $this->resetSelection();
        $this->dispatch('notify', ['message' => __('Riwayat kandidat berhasil dihapus: :count.', ['count' => $deletedCount]), 'type' => 'success']);
    }

    public function exportCsv()
    {
        $filename = "export_kandidat_{$this->tab}_".now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Nama Kandidat', 'Email', 'Posisi', 'Departemen', 'Site', 'Tanggal Lamar', 'Stage', 'Status Lolos Administrasi']);

            $this->getFilteredQuery()->chunk(500, function ($applications) use ($output): void {
                foreach ($applications as $app) {
                    fputcsv($output, [
                        $app->candidate->name,
                        $app->candidate->email,
                        $app->job->title,
                        $app->job->department?->name ?? '',
                        $app->job->site?->name ?? '',
                        $app->created_at->format('Y-m-d'),
                        $app->recruitment_stage->name,
                        $app->stage_updated_at?->format('Y-m-d') ?? '',
                    ]);
                }
            });

            fclose($output);
        }, $filename);
    }

    public function openScheduleInterview(int $applicationId): void
    {
        $this->schedulingApplicationId = $applicationId;
        $interview = Interview::where('application_id', $applicationId)
            ->where('interview_type', 'HR Interview')
            ->first();

        $this->interviewer_id = $interview?->interviewer_id;
        $this->scheduled_date = $interview?->scheduled_at?->format('Y-m-d') ?? '';
        $this->scheduled_time = $interview?->scheduled_at?->format('H:i') ?? '';
        $this->hr_notes = (string) ($interview?->hr_notes ?? '');
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

        $scheduledAt = Carbon::parse($validated['scheduled_date'].' '.$validated['scheduled_time']);
        $application = Application::with(['candidate', 'job.department'])->findOrFail($validated['schedulingApplicationId']);

        if (! in_array($application->recruitment_stage, [RecruitmentStage::ADMINISTRASI, RecruitmentStage::HR_INTERVIEW], true)) {
            $this->dispatch('notify', ['message' => __('Kandidat harus sudah lolos administrasi sebelum dijadwalkan HR Interview.'), 'type' => 'error']);

            return;
        }

        $interview = Interview::updateOrCreate(
            [
                'application_id' => $application->id,
                'interview_type' => 'HR Interview',
            ],
            [
                'interviewer_id' => $validated['interviewer_id'],
                'status' => 'scheduled',
                'scheduled_at' => $scheduledAt,
                'hr_notes' => $validated['hr_notes'],
            ]
        );

        if ($application->recruitment_stage !== RecruitmentStage::HR_INTERVIEW) {
            $oldStage = $application->recruitment_stage;

            $application->update([
                'recruitment_stage' => RecruitmentStage::HR_INTERVIEW,
                'stage_updated_at' => now(),
            ]);

            ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage->value,
                'decision' => 'passed',
                'notes' => 'HR Interview dijadwalkan',
                'decided_by' => Auth::id(),
            ]);
        }

        app(RecruitmentNotificationService::class)->notifyDatabaseAndMail(
            $application->candidate,
            __('Jadwal Interview HR'),
            __('Interview HR untuk posisi :job dijadwalkan pada :date.', [
                'job' => $application->job->title,
                'date' => $scheduledAt->format('d M Y H:i'),
            ]),
            route('candidate.applications'),
            'interview_scheduled'
        );

        app(RecruitmentNotificationService::class)->notifyDatabaseAndMail(
            $interview->interviewer,
            __('Jadwal Interview HR'),
            __('Anda ditugaskan mewawancarai :candidate untuk posisi :job pada :date.', [
                'candidate' => $application->candidate->name,
                'job' => $application->job->title,
                'date' => $scheduledAt->format('d M Y H:i'),
            ]),
            route('interviews.hr'),
            'interview_scheduled'
        );

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
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterDepartment !== '') {
            $query->whereHas('job', fn ($q) => $q->where('department_id', (int) $this->filterDepartment));
        }

        if ($this->filterSite !== '') {
            $query->whereHas('job', fn ($q) => $q->where('site_id', (int) $this->filterSite));
        }

        // Use filterStage if set, otherwise fall back to filterStatus.
        // Both target the same column so they are merged into one check.
        $stageFilter = $this->filterStage !== '' ? $this->filterStage : $this->filterStatus;
        if ($stageFilter !== '') {
            $query->where('recruitment_stage', $stageFilter);
        }

        // Administrasi: candidates still in admin screening (already applied).
        if ($this->tab === 'administrasi') {
            $query->where('recruitment_stage', RecruitmentStage::APPLIED);
        }

        // On Progress: candidates who passed administration and are waiting for HR interview scheduling.
        if ($this->tab === 'on-progress') {
            $query->where('recruitment_stage', RecruitmentStage::ADMINISTRASI);
        }

        return $query;
    }

    private function currentUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
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
                RecruitmentStage::ADMINISTRASI,
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
