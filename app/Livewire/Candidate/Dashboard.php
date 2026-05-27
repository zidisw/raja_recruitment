<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && $user->hasUserRole(), 403);
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id;

        $myApplications = Application::query()->where('user_id', $userId);

        $ongoingCount = (clone $myApplications)
            ->whereNotIn('recruitment_stage', [RecruitmentStage::REJECTED, RecruitmentStage::HIRED])
            ->count();

        $stats = [
            'open_jobs' => Job::query()->where('is_active', true)->count(),
            'my_total' => (clone $myApplications)->count(),
            'my_ongoing' => $ongoingCount,
            'my_hired' => (clone $myApplications)->where('recruitment_stage', RecruitmentStage::HIRED)->count(),
            'remaining_slots' => max(0, 2 - $ongoingCount),
        ];

        $latestApplications = Application::query()
            ->with('job')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('livewire.candidate.dashboard', [
            'stats' => $stats,
            'latestApplications' => $latestApplications,
        ]);
    }
}
