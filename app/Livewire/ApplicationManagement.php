<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Exports\ApplicationsExport;
use App\Jobs\ProcessBulkCandidateEmails;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class ApplicationManagement extends Component
{
    use WithPagination;

    public Job $job;

    public string $search = '';

    public array $statusFilter = [];

    public array $genderFilter = [];

    public array $religionFilter = [];

    public array $degreeFilter = [];

    public string $hasExperience = '';

    public string $hasOrganization = '';

    public array $documentsFilter = [];

    public int $perPage = 10;

    public ?int $expandedRow = null;

    // Bulk Email
    public bool $showBulkEmailModal = false;

    public string $bulkEmailStage = '';

    public bool $bulkEmailActiveOnly = true;

    public string $bulkEmailSubject = '';

    public string $bulkEmailBody = '';

    public int $bulkEmailStep = 1;

    // Bulk Reject
    public bool $showBulkRejectModal = false;

    public string $bulkRejectStage = '';

    public string $bulkRejectNotes = '';

    public int $bulkRejectStep = 1;

    public string $bulkRejectConfirmText = '';

    public function mount(Job $job): void
    {
        $user = auth()->user();

        abort_unless($user->canAccessRecruitment(), 403);

        $this->job = $job;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatingReligionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDegreeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingHasExperience(): void
    {
        $this->resetPage();
    }

    public function updatingHasOrganization(): void
    {
        $this->resetPage();
    }

    public function updatingDocumentsFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedRow = $this->expandedRow === $id ? null : $id;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = [];
        $this->genderFilter = [];
        $this->religionFilter = [];
        $this->degreeFilter = [];
        $this->hasExperience = '';
        $this->hasOrganization = '';
        $this->documentsFilter = [];
        $this->resetPage();
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new ApplicationsExport(
                jobId: $this->job->id,
                search: $this->search,
                statusFilter: $this->statusFilter,
                genderFilter: $this->genderFilter,
                religionFilter: $this->religionFilter,
                degreeFilter: $this->degreeFilter,
                hasExperience: $this->hasExperience,
                hasOrganization: $this->hasOrganization,
                documentsFilter: $this->documentsFilter,
            ),
            'applicants-'.str($this->job->title)->slug().'-'.now()->format('Ymd').'.xlsx'
        );
    }

    // ─── Bulk Email ───────────────────────────────────────────────────────────

    public function openBulkEmail(): void
    {
        $this->bulkEmailStage = '';
        $this->bulkEmailActiveOnly = true;
        $this->bulkEmailSubject = '';
        $this->bulkEmailBody = '';
        $this->bulkEmailStep = 1;
        $this->showBulkEmailModal = true;
    }

    public function bulkEmailRecipientsQuery(): Builder
    {
        $query = Application::with('candidate')
            ->where('job_id', $this->job->id);

        if ($this->bulkEmailStage !== '') {
            $query->where('recruitment_stage', $this->bulkEmailStage);
        }

        if ($this->bulkEmailActiveOnly) {
            $query->whereNotIn('recruitment_stage', [
                RecruitmentStage::REJECTED->value,
                RecruitmentStage::HIRED->value,
            ]);
        }

        return $query;
    }

    public function proceedBulkEmail(): void
    {
        $this->validate([
            'bulkEmailSubject' => ['required', 'string', 'max:255'],
            'bulkEmailBody' => ['required', 'string', 'max:5000'],
        ]);

        $this->bulkEmailStep = 2;
    }

    public function sendBulkEmail(): void
    {
        $applicationIds = $this->bulkEmailRecipientsQuery()->pluck('id')->toArray();

        ProcessBulkCandidateEmails::dispatch(
            $applicationIds,
            $this->bulkEmailSubject,
            $this->bulkEmailBody,
            $this->job->title
        );

        $this->showBulkEmailModal = false;
        $this->bulkEmailStep = 1;
        $this->dispatch('notify', ['message' => __('Bulk email process started in the background.'), 'type' => 'success']);
    }

    // ─── Bulk Reject ──────────────────────────────────────────────────────────

    public function openBulkReject(): void
    {
        $this->bulkRejectStage = '';
        $this->bulkRejectNotes = '';
        $this->bulkRejectStep = 1;
        $this->bulkRejectConfirmText = '';
        $this->showBulkRejectModal = true;
    }

    public function proceedBulkRejectStep2(): void
    {
        $this->validate([
            'bulkRejectStage' => ['required'],
        ]);

        $this->bulkRejectStep = 2;
    }

    public function proceedBulkRejectStep3(): void
    {
        $this->validate([
            'bulkRejectNotes' => ['nullable', 'string', 'min:5', 'max:2000'],
        ]);

        $this->bulkRejectConfirmText = '';
        $this->bulkRejectStep = 3;
    }

    /**
     * Get pipeline stages that come before the given threshold stage.
     */
    private function stagesBelowThreshold(string $thresholdStageValue): array
    {
        $pipeline = RecruitmentStage::pipelineStages();
        $thresholdStage = RecruitmentStage::from($thresholdStageValue);
        $below = [];

        foreach ($pipeline as $stage) {
            if ($stage === $thresholdStage) {
                break;
            }
            $below[] = $stage->value;
        }

        return $below;
    }

    public function executeBulkReject(): void
    {
        if ($this->bulkRejectConfirmText !== 'REJECT') {
            return;
        }

        $belowStages = $this->stagesBelowThreshold($this->bulkRejectStage);

        $toRejectIds = Application::where('job_id', $this->job->id)
            ->whereIn('recruitment_stage', $belowStages)
            ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED->value, RecruitmentStage::HIRED->value])
            ->pluck('id')
            ->toArray();

        if (empty($toRejectIds)) {
            $this->showBulkRejectModal = false;

            return;
        }

        $now = now();
        $notes = filled($this->bulkRejectNotes)
            ? trim($this->bulkRejectNotes).' [Bulk Rejection — '.$now->format('d M Y').']'
            : 'Tidak lolos seleksi massal — '.$now->format('d M Y H:i');

        app(ApplicationService::class)->bulkReject($toRejectIds, auth()->id(), $notes);

        $this->showBulkRejectModal = false;
        $this->bulkRejectStep = 1;
        $this->expandedRow = null;

        $this->dispatch('notify', ['message' => __('Bulk reject completed. :count candidates marked as Not Selected.', ['count' => count($toRejectIds)]), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();

        $query = Application::with(['candidate.profile', 'stageLogs'])
            ->where('job_id', $this->job->id)
            ->latest('stage_updated_at');

        if ($this->search) {
            $query->whereHas('candidate', function ($q): void {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->whereIn('recruitment_stage', $this->statusFilter);
        }

        if ($this->genderFilter) {
            $query->whereHas('candidate.profile', fn ($q) => $q->whereIn('gender', $this->genderFilter));
        }

        if ($this->religionFilter) {
            $query->whereHas('candidate.profile', fn ($q) => $q->whereIn('religion', $this->religionFilter));
        }

        if ($this->degreeFilter) {
            $query->whereHas('candidate.education', fn ($q) => $q->whereIn('degree', $this->degreeFilter));
        }

        if ($this->hasExperience === 'yes') {
            $query->whereHas('candidate.experiences');
        } elseif ($this->hasExperience === 'no') {
            $query->whereDoesntHave('candidate.experiences');
        }

        if ($this->hasOrganization === 'yes') {
            $query->whereHas('candidate.organizations');
        } elseif ($this->hasOrganization === 'no') {
            $query->whereDoesntHave('candidate.organizations');
        }

        $allowedDocs = ['ktp', 'portfolio', 'certificate', 'paklaring'];
        foreach ($this->documentsFilter as $doc) {
            if (in_array($doc, $allowedDocs, true)) {
                $query->whereHas('candidate.profile', fn ($q) => $q->whereNotNull("{$doc}_path"));
            }
        }

        $applications = $query->paginate($this->perPage);

        $expandedData = null;
        if ($this->expandedRow) {
            $expandedData = Application::with([
                'candidate.profile',
                'candidate.education',
                'candidate.experiences',
                'candidate.organizations',
            ])->find($this->expandedRow);
        }

        // Bulk email recipient count (live)
        $bulkEmailCount = $this->showBulkEmailModal
            ? $this->bulkEmailRecipientsQuery()->count()
            : 0;

        // Bulk reject preview
        $bulkRejectCount = 0;
        $bulkRejectPreview = collect();
        $bulkRejectSafeCount = 0;
        if ($this->showBulkRejectModal && $this->bulkRejectStage !== '' && $this->bulkRejectStep >= 2) {
            $belowStages = $this->stagesBelowThreshold($this->bulkRejectStage);
            $bulkRejectPreview = Application::with('candidate.profile')
                ->where('job_id', $this->job->id)
                ->whereIn('recruitment_stage', $belowStages)
                ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED->value, RecruitmentStage::HIRED->value])
                ->limit(50)
                ->get();
            $bulkRejectCount = $bulkRejectPreview->count() < 50
                ? $bulkRejectPreview->count()
                : Application::where('job_id', $this->job->id)
                    ->whereIn('recruitment_stage', $belowStages)
                    ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED->value, RecruitmentStage::HIRED->value])
                    ->count();

            $pipeline = RecruitmentStage::pipelineStages();
            $thresholdStage = RecruitmentStage::from($this->bulkRejectStage);
            $atOrAbove = [];
            $found = false;
            foreach ($pipeline as $stage) {
                if ($stage === $thresholdStage) {
                    $found = true;
                }
                if ($found) {
                    $atOrAbove[] = $stage->value;
                }
            }

            $bulkRejectSafeCount = Application::where('job_id', $this->job->id)
                ->whereIn('recruitment_stage', $atOrAbove)
                ->count();
        }

        // Bulk email preview recipients
        $bulkEmailPreview = collect();
        if ($this->showBulkEmailModal && $this->bulkEmailStep === 2) {
            $bulkEmailPreview = $this->bulkEmailRecipientsQuery()
                ->with('candidate')
                ->limit(200)
                ->get();
        }

        return view('livewire.application-management', [
            'applications' => $applications,
            'statuses' => RecruitmentStage::cases(),
            'expandedData' => $expandedData,
            'bulkEmailCount' => $bulkEmailCount,
            'bulkEmailPreview' => $bulkEmailPreview,
            'bulkRejectCount' => $bulkRejectCount,
            'bulkRejectPreview' => $bulkRejectPreview,
            'bulkRejectSafeCount' => $bulkRejectSafeCount,
        ]);
    }
}
