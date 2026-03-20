<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\OfferingLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OfferingLetterManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    public ?int $application_id = null;
    public string $offer_date = '';
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
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'offer_date' => ['required', 'date'],
            'status' => ['required', 'in:waiting_response,accepted,rejected'],
        ]);

        $offering = OfferingLetter::updateOrCreate(
            ['application_id' => $validated['application_id']],
            $validated
        );

        $application = $offering->application;

        if ($validated['status'] === 'accepted') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::PSYCHOTEST,
                'stage_updated_at' => now(),
            ]);
        }

        if ($validated['status'] === 'rejected') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
        }

        if ($validated['status'] === 'waiting_response') {
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
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'application_id', 'offer_date']);
        $this->status = 'waiting_response';
    }
}
