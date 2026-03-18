<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyApplications extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasUserRole(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with(['job.department', 'job.site', 'stageLogs.decidedBy'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($this->search) {
            $query->whereHas('job', function ($q): void {
                $q->where('title', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('recruitment_stage', $this->statusFilter);
        }

        return view('livewire.candidate.my-applications', [
            'applications' => $query->paginate(10),
            'statuses' => RecruitmentStage::cases(),
        ]);
    }
}
