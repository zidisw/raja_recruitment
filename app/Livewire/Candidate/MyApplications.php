<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\OfferingStatus;
use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyApplications extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public string $tab = 'on_progress';

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasUserRole(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTab(): void
    {
        $this->resetPage();
    }

    public function acceptOffer(int $applicationId): void
    {
        $application = Application::where('user_id', '=', Auth::id())->findOrFail($applicationId);
        
        if ($application->recruitment_stage !== RecruitmentStage::OFFERING) {
            $this->dispatch('notify', ['message' => __('Status lamaran tidak valid.'), 'type' => 'error']);
            return;
        }

        $offering = $application->offeringLetter;
        if (!$offering) {
            $this->dispatch('notify', ['message' => __('Offering letter tidak ditemukan.'), 'type' => 'error']);
            return;
        }

        $offering->update(['status' => OfferingStatus::ACCEPTED->value]);
        $application->update([
            'recruitment_stage' => RecruitmentStage::PSYCHOTEST,
            'stage_updated_at' => now(),
        ]);

        $this->dispatch('notify', ['message' => __('Selamat! Anda telah menerima penawaran pekerjaan ini.'), 'type' => 'success']);
    }

    public function rejectOffer(int $applicationId): void
    {
        $application = Application::where('user_id', '=', Auth::id())->findOrFail($applicationId);
        
        if ($application->recruitment_stage !== RecruitmentStage::OFFERING) {
            $this->dispatch('notify', ['message' => __('Status lamaran tidak valid.'), 'type' => 'error']);
            return;
        }

        $offering = $application->offeringLetter;
        if (!$offering) {
            $this->dispatch('notify', ['message' => __('Offering letter tidak ditemukan.'), 'type' => 'error']);
            return;
        }

        $offering->update(['status' => OfferingStatus::REJECTED->value]);
        $application->update([
            'recruitment_stage' => RecruitmentStage::REJECTED,
            'stage_updated_at' => now(),
        ]);

        $this->dispatch('notify', ['message' => __('Anda telah menolak penawaran pekerjaan ini.'), 'type' => 'info']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with(['job.department', 'job.site', 'stageLogs.decidedBy', 'offeringLetter'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($this->search) {
            $query->whereHas('job', function ($q): void {
                $q->where('title', 'like', "%{$this->search}%");
            });
        }

        if ($this->tab === 'on_progress') {
            $query->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED, RecruitmentStage::HIRED]);
        } elseif ($this->tab === 'hired') {
            $query->where('recruitment_stage', RecruitmentStage::HIRED);
        } elseif ($this->tab === 'history') {
            $query->where('recruitment_stage', RecruitmentStage::REJECTED);
        }

        return view('livewire.candidate.my-applications', [
            'applications' => $query->paginate(10),
            'statuses' => RecruitmentStage::cases(),
        ]);
    }
}
