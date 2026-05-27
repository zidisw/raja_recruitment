<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\Department;
use App\Models\Mcu;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class McuManagement extends Component
{
    use WithFileUploads, WithPagination;

    public bool $showModal = false;

    public ?int $expandedRow = null;

    public ?int $editingId = null;

    public ?int $application_id = null;

    public string $mcu_date = '';

    public string $result = 'fit';

    public string $notes = '';

    public $mcu_file;

    public string $search = '';

    public string $filterDepartment = '';

    public string $filterSite = '';

    public string $filterResult = '';

    public int $perPage = 10;

    #[Computed]
    public function currentMcu(): ?Mcu
    {
        if (! $this->editingId) {
            return null;
        }

        return Mcu::find($this->editingId);
    }

    #[Computed]
    public function lockedApplication(): ?Application
    {
        if (! $this->application_id) {
            return null;
        }

        return Application::with(['candidate', 'job'])->find($this->application_id);
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
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

    public function updatingFilterResult(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openCreate(?int $appId = null): void
    {
        $this->reset(['editingId', 'application_id', 'mcu_date', 'notes', 'mcu_file']);
        if ($appId) {
            $this->application_id = $appId;
        }
        $this->result = 'fit';
        $this->showModal = true;
    }

    public function openEdit(Mcu $mcu): void
    {
        $this->editingId = $mcu->id;
        $this->application_id = $mcu->application_id;
        $this->mcu_date = $mcu->mcu_date ? Carbon::parse($mcu->mcu_date)->format('Y-m-d') : '';
        $this->result = $mcu->result;
        $this->notes = (string) $mcu->notes;
        $this->mcu_file = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'mcu_date' => ['required', 'date'],
            'result' => ['required', 'in:fit,fit_with_notes,unfit'],
            'notes' => ['nullable', 'string', 'max:2000', 'required_if:result,fit_with_notes'],
            'mcu_file' => [$this->editingId ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:5120'],
        ]);
        $data = [
            'application_id' => $validated['application_id'],
            'mcu_date' => $validated['mcu_date'],
            'result' => $validated['result'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($this->mcu_file) {
            $data['file_path'] = $this->mcu_file->store('mcu-results', 'public');
        }

        $mcu = Mcu::updateOrCreate(['application_id' => $validated['application_id']], $data);

        $application = $mcu->application;
        $oldStage = $application->recruitment_stage->value;

        if (in_array($validated['result'], ['fit', 'fit_with_notes'], true)) {
            $application->update([
                'recruitment_stage' => RecruitmentStage::ONBOARDING,
                'stage_updated_at' => now(),
            ]);
            \App\Models\ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage,
                'decision' => 'passed',
                'notes' => $validated['result'] === 'fit_with_notes'
                    ? 'Hasil MCU: Fit With Notes. '.($validated['notes'] ?? '')
                    : 'Hasil MCU: Fit to Work',
                'decided_by' => Auth::id(),
            ]);
        } else {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
            \App\Models\ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage,
                'decision' => 'rejected',
                'notes' => 'Hasil MCU: Unfit. '.($validated['notes'] ?? ''),
                'decided_by' => Auth::id(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => __('MCU result saved.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with(['candidate', 'job.department', 'job.site', 'mcu'])
            ->whereIn('recruitment_stage', [RecruitmentStage::MCU, RecruitmentStage::ONBOARDING])
            ->latest('updated_at');

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

        if ($this->filterResult !== '') {
            if ($this->filterResult === 'none') {
                $query->whereDoesntHave('mcu');
            } else {
                $query->whereHas('mcu', fn ($q) => $q->where('result', $this->filterResult));
            }
        }

        return view('livewire.mcu-management', [
            'applications_paginated' => $query->paginate($this->perPage),
            'departments' => Cache::remember('ref.departments', 300, fn () => Department::query()->orderBy('name')->get(['id', 'name'])),
            'sites' => Cache::remember('ref.sites', 300, fn () => Site::query()->orderBy('name')->get(['id', 'name'])),
        ]);
    }
}
