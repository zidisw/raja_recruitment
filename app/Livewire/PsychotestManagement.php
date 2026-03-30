<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Psychotest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PsychotestManagement extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;
    public ?int $expandedRow = null;
    public ?int $editingId = null;
    public ?int $application_id = null;
    public string $test_date = '';
    public string $result = 'passed';
    public string $notes = '';
    public $psychotest_file;

    #[Computed]
    public function currentPsychotest(): ?Psychotest
    {
        if (! $this->editingId) {
            return null;
        }
        return Psychotest::find($this->editingId);
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

    public function openCreate(?int $appId = null): void
    {
        $this->reset(['editingId', 'application_id', 'test_date', 'notes', 'psychotest_file']);
        if ($appId) {
            $this->application_id = $appId;
        }
        $this->result = 'passed';
        $this->showModal = true;
    }

    public function openEdit(Psychotest $psychotest): void
    {
        $this->editingId = $psychotest->id;
        $this->application_id = $psychotest->application_id;
        $this->test_date = $psychotest->test_date?->format('Y-m-d') ?? '';
        $this->result = $psychotest->result;
        $this->notes = (string) $psychotest->notes;
        $this->psychotest_file = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'test_date' => ['required', 'date'],
            'result' => ['required', 'in:passed,failed'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'psychotest_file' => [$this->editingId ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $data = [
            'application_id' => $validated['application_id'],
            'test_date' => $validated['test_date'],
            'result' => $validated['result'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($this->psychotest_file) {
            $data['file_path'] = $this->psychotest_file->store('psychotest-results', 'public');
        }

        $psychotest = Psychotest::updateOrCreate(
            ['application_id' => $validated['application_id']],
            $data
        );

        $application = $psychotest->application;
        $oldStage = $application->recruitment_stage->value;

        if ($validated['result'] === 'passed') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::MCU,
                'stage_updated_at' => now(),
            ]);
            \App\Models\ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage,
                'decision' => 'passed',
                'notes' => 'Hasil Psychotest: Passed',
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
                'notes' => 'Hasil Psychotest: Failed. ' . ($validated['notes'] ?? ''),
                'decided_by' => Auth::id(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => __('Psychotest result saved.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.psychotest-management', [
            'applications_paginated' => Application::with(['candidate', 'job', 'psychotest'])
                ->whereIn('recruitment_stage', [RecruitmentStage::PSYCHOTEST, RecruitmentStage::MCU])
                ->latest('updated_at')
                ->paginate(10),
        ]);
    }
}
