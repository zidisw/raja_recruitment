<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\OfferingStatus;
use App\Enums\RecruitmentStage;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyApplications extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    #[Url]
    public string $tab = 'on_progress';

    public ?int $uploadingForApplicationId = null;

    public $signed_ol_file = null;

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
        if (! $offering) {
            $this->dispatch('notify', ['message' => __('Offering letter tidak ditemukan.'), 'type' => 'error']);

            return;
        }

        // Check if signed OL has been uploaded
        if (! $offering->signed_file_path) {
            $this->dispatch('notify', ['message' => __('Harap unggah OL yang sudah ditandatangani sebelum menerima penawaran.'), 'type' => 'warning']);

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
        if (! $offering) {
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

    public function uploadSignedOL(int $applicationId): void
    {
        $validated = $this->validate([
            'signed_ol_file' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ], [], [
            'signed_ol_file' => __('Signed OL'),
        ]);

        $application = Application::where('user_id', '=', Auth::id())->findOrFail($applicationId);
        $offering = $application->offeringLetter;

        if (! $offering) {
            $this->dispatch('notify', ['message' => __('Offering letter tidak ditemukan.'), 'type' => 'error']);

            return;
        }

        if ($this->signed_ol_file) {
            $signed_path = $this->signed_ol_file->store('offering-letters/signed', 'public');
            $offering->update([
                'signed_file_path' => $signed_path,
                'signed_at' => now(),
            ]);
        }

        $this->signed_ol_file = null;
        $this->uploadingForApplicationId = null;
        $this->dispatch('notify', ['message' => __('OL yang sudah ditandatangani berhasil diunggah.'), 'type' => 'success']);
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
