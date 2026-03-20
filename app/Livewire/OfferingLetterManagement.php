<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\OfferingStatus;
use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\OfferingLetter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OfferingLetterManagement extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;
    public ?int $editingId = null;

    public ?int $application_id = null;
    public string $offer_date = '';
    public $offer_file; // For uploading new file
    public string $status = 'waiting_response';

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function openCreate(?int $appId = null): void
    {
        $this->resetForm();
        if ($appId) {
            $this->application_id = $appId;
        }
        $this->showModal = true;
    }

    public function openEdit(OfferingLetter $offering): void
    {
        $this->editingId = $offering->id;
        $this->application_id = $offering->application_id;
        $this->offer_date = $offering->offer_date?->format('Y-m-d') ?? '';
        $this->status = $offering->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'application_id' => ['required', 'exists:applications,id'],
            'offer_date' => ['required', 'date'],
            'status' => ['required', 'in:waiting_response,accepted,rejected'],
            'offer_file' => [$this->editingId ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:5120'],
        ];

        $validated = $this->validate($rules);

        $data = [
            'application_id' => $validated['application_id'],
            'offer_date' => $validated['offer_date'],
            'status' => $validated['status'],
        ];

        if ($this->offer_file) {
            $data['file_path'] = $this->offer_file->store('offering-letters', 'public');
        }

        $offering = OfferingLetter::updateOrCreate(
            ['application_id' => $validated['application_id']],
            $data
        );

        $application = $offering->application;

        // Sync Application Stage based on Offering Status
        if ($validated['status'] === OfferingStatus::ACCEPTED->value) {
            $application->update([
                'recruitment_stage' => RecruitmentStage::PSYCHOTEST,
                'stage_updated_at' => now(),
            ]);
        } elseif ($validated['status'] === OfferingStatus::REJECTED->value) {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
        } else {
            // waiting_response
            $application->update([
                'recruitment_stage' => RecruitmentStage::OFFERING,
                'stage_updated_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('notify', ['message' => __('Offering letter saved successfully.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.offering-letter-management', [
            'applications_paginated' => Application::with(['candidate', 'job', 'offeringLetter'])
                ->whereIn('recruitment_stage', [RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST])
                ->latest('updated_at')
                ->paginate(10),
            'applications' => Application::with(['candidate', 'job'])
                ->whereIn('recruitment_stage', [RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST])
                ->orWhereHas('offeringLetter')
                ->get(),
            'statuses' => OfferingStatus::cases(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'application_id', 'offer_date', 'offer_file']);
        $this->status = 'waiting_response';
    }
}
