<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\Department;
use App\Models\Onboarding;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OnboardingManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $expandedRow = null;

    public ?int $editingOnboardingId = null;

    public ?int $application_id = null;

    public string $joining_date = '';

    public string $onboarding_status = 'pending';

    public string $travel_ticket_number = '';

    public string $travel_ticket_notes = '';

    public string $onsite_date = '';

    public string $onsite_location = '';

    public string $onsite_notes = '';

    public string $search = '';

    public string $filterDepartment = '';

    public string $filterSite = '';

    public string $filterStatus = '';

    public int $perPage = 10;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->canAccessRecruitment(), 403);
    }

    public function updatingSearch(): void
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

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function openCreate(?int $applicationId = null): void
    {
        $this->reset(['editingOnboardingId', 'application_id', 'joining_date', 'travel_ticket_number', 'travel_ticket_notes', 'onsite_date', 'onsite_location', 'onsite_notes']);
        $this->application_id = $applicationId;
        $this->onboarding_status = 'pending';
        $this->showModal = true;
    }

    public function openEdit(int $onboardingId): void
    {
        $onboarding = Onboarding::findOrFail($onboardingId);

        $this->editingOnboardingId = $onboarding->id;
        $this->application_id = $onboarding->application_id;
        $this->joining_date = $onboarding->joining_date
            ? Carbon::parse($onboarding->joining_date)->toDateString()
            : '';
        $this->onboarding_status = $onboarding->onboarding_status;
        $this->travel_ticket_number = (string) ($onboarding->travel_ticket_number ?? '');
        $this->travel_ticket_notes = (string) ($onboarding->travel_ticket_notes ?? '');
        $this->onsite_date = $onboarding->onsite_date
            ? Carbon::parse($onboarding->onsite_date)->toDateString()
            : '';
        $this->onsite_location = (string) ($onboarding->onsite_location ?? '');
        $this->onsite_notes = (string) ($onboarding->onsite_notes ?? '');
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'joining_date' => ['required', 'date'],
            'onboarding_status' => ['required', 'in:pending,completed'],
            'travel_ticket_number' => ['nullable', 'string', 'max:255'],
            'travel_ticket_notes' => ['nullable', 'string', 'max:2000'],
            'onsite_date' => ['nullable', 'date'],
            'onsite_location' => ['nullable', 'string', 'max:255'],
            'onsite_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $onboarding = Onboarding::updateOrCreate(
            ['application_id' => $validated['application_id']],
            [
                'joining_date' => $validated['joining_date'],
                'onboarding_status' => $validated['onboarding_status'],
                'travel_ticket_number' => $validated['travel_ticket_number'] ?: null,
                'travel_ticket_notes' => $validated['travel_ticket_notes'] ?: null,
                'travel_ticket_sent_at' => $validated['travel_ticket_number'] ? now() : null,
                'onsite_date' => $validated['onsite_date'] ?: null,
                'onsite_location' => $validated['onsite_location'] ?: null,
                'onsite_notes' => $validated['onsite_notes'] ?: null,
            ]
        );

        $application = $onboarding->application;
        $oldStage = $application->recruitment_stage;
        $targetStage = $validated['onboarding_status'] === 'completed'
            ? RecruitmentStage::HIRED
            : RecruitmentStage::ONBOARDING;

        if ($oldStage !== $targetStage) {
            $application->update([
                'recruitment_stage' => $targetStage,
                'stage_updated_at' => now(),
            ]);

            ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage->value,
                'decision' => 'passed',
                'notes' => $targetStage === RecruitmentStage::HIRED
                    ? 'Onboarding completed'
                    : 'Onboarding in progress',
                'decided_by' => Auth::id() ?? $application->user_id,
            ]);
        }

        $this->showModal = false;
        $this->reset(['editingOnboardingId', 'application_id', 'joining_date', 'travel_ticket_number', 'travel_ticket_notes', 'onsite_date', 'onsite_location', 'onsite_notes']);
        $this->onboarding_status = 'pending';
        $this->dispatch('notify', ['message' => __('Onboarding saved successfully.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with(['candidate', 'job.department', 'job.site', 'onboarding'])
            ->where(function ($query): void {
                $query->whereIn('recruitment_stage', [RecruitmentStage::ONBOARDING, RecruitmentStage::HIRED])
                    ->orWhereHas('onboarding');
            })
            ->latest('stage_updated_at');

        if ($this->search !== '') {
            $query->where(function ($q): void {
                $q->whereHas('candidate', function ($candidate): void {
                    $candidate->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })->orWhereHas('job', function ($job): void {
                    $job->where('title', 'like', '%'.$this->search.'%');
                });
            });
        }

        if ($this->filterDepartment !== '') {
            $query->whereHas('job', fn ($q) => $q->where('department_id', (int) $this->filterDepartment));
        }

        if ($this->filterSite !== '') {
            $query->whereHas('job', fn ($q) => $q->where('site_id', (int) $this->filterSite));
        }

        if ($this->filterStatus !== '') {
            if ($this->filterStatus === 'none') {
                $query->whereDoesntHave('onboarding');
            } else {
                $query->whereHas('onboarding', fn ($q) => $q->where('onboarding_status', $this->filterStatus));
            }
        }

        return view('livewire.onboarding-management', [
            'applicationsPaginated' => $query->paginate($this->perPage),
            'applications' => Application::with(['candidate', 'job'])
                ->where(function ($query): void {
                    $query->whereIn('recruitment_stage', [RecruitmentStage::ONBOARDING, RecruitmentStage::HIRED])
                        ->orWhereHas('onboarding');
                })
                ->get(),
            'departments' => Cache::remember('ref.departments', 300, fn () => Department::query()->orderBy('name')->get(['id', 'name'])),
            'sites' => Cache::remember('ref.sites', 300, fn () => Site::query()->orderBy('name')->get(['id', 'name'])),
        ]);
    }
}
