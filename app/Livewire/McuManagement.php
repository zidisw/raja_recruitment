<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Mcu;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class McuManagement extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;
    public ?int $editingId = null;
    public ?int $application_id = null;
    public string $mcu_date = '';
    public string $result = 'fit';
    public string $notes = '';
    public $mcu_file;

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

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
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
        $this->mcu_date = $mcu->mcu_date?->format('Y-m-d') ?? '';
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
            'result' => ['required', 'in:fit,unfit'],
            'notes' => ['nullable', 'string', 'max:2000'],
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
        if ($validated['result'] === 'fit') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::ONBOARDING,
                'stage_updated_at' => now(),
            ]);
        } else {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => __('MCU result saved.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.mcu-management', [
            'applications_paginated' => Application::with(['candidate', 'job', 'mcu'])
                ->whereIn('recruitment_stage', [RecruitmentStage::MCU, RecruitmentStage::ONBOARDING])
                ->latest('updated_at')
                ->paginate(10),
        ]);
    }
}
