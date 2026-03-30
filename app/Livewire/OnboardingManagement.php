<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Onboarding;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OnboardingManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $expandedRow = null;
    public ?int $application_id = null;
    public string $joining_date = '';
    public string $onboarding_status = 'pending';

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function openCreate(): void
    {
        $this->reset(['application_id', 'joining_date']);
        $this->onboarding_status = 'pending';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'joining_date' => ['required', 'date'],
            'onboarding_status' => ['required', 'in:pending,completed'],
        ]);

        $onboarding = Onboarding::updateOrCreate(['application_id' => $validated['application_id']], $validated);

        $application = $onboarding->application;
        if ($validated['onboarding_status'] === 'completed') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::HIRED,
                'stage_updated_at' => now(),
            ]);
        } else {
            $application->update([
                'recruitment_stage' => RecruitmentStage::ONBOARDING,
                'stage_updated_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => __('Onboarding saved successfully.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.onboarding-management', [
            'onboardings' => Onboarding::with('application.candidate', 'application.job')
                ->latest('joining_date')
                ->paginate(10),
            'applications' => Application::with(['candidate', 'job'])
                ->whereIn('recruitment_stage', [RecruitmentStage::ONBOARDING, RecruitmentStage::HIRED])
                ->orWhereHas('onboarding')
                ->get(),
        ]);
    }
}
