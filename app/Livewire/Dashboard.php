<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use App\Models\OfferingLetter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Candidates have their own portal — redirect them there
        if ($user && method_exists($user, 'hasUserRole') && $user->hasUserRole()) {
            $this->redirect(route('candidate.dashboard'));
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $stats = Cache::remember(
            sprintf('dashboard.stats.%s.%s', $user->id, $user->role->value),
            now()->addMinutes(5),
            function () use ($user) {
                $stats = [];

                if ($user->isSuperAdmin()) {
                    $stats = [
                        'total_applications' => Application::count(),
                        'active_jobs' => Job::where('is_active', true)->count(),
                        'total_candidates' => User::query()->whereIn('role', [UserRole::User, UserRole::Candidate])->count(),
                        'hired_this_month' => Application::where('recruitment_stage', RecruitmentStage::HIRED)
                            ->whereMonth('stage_updated_at', now()->month)
                            ->count(),
                        'pipeline' => Application::selectRaw('recruitment_stage, count(*) as total')
                            ->groupBy('recruitment_stage')
                            ->get()
                            ->keyBy('recruitment_stage'),

                        // Chart Data
                        'funnel_chart' => Application::selectRaw('recruitment_stage, count(*) as total')
                            ->where('recruitment_stage', '!=', RecruitmentStage::REJECTED)
                            ->groupBy('recruitment_stage')
                            ->get()
                            ->map(fn($item) => [
                                'name' => $item->recruitment_stage instanceof RecruitmentStage
                                    ? $item->recruitment_stage->label()
                                    : RecruitmentStage::from($item->recruitment_stage)->label(),
                                'data' => [(int) $item->total],
                            ])
                            ->values(),

                        'monthly_trend' => $this->monthlyTrend(),
                    ];
                } elseif ($user->hasAdminRole()) {
                    $jobIds = Job::query()->pluck('id');

                    $stats = [
                        'total_open_jobs' => Job::where('is_active', true)->count(),
                        'total_applicants' => Application::whereIn('job_id', $jobIds)->count(),
                        'administrative_passed' => Application::whereIn('job_id', $jobIds)
                            ->whereIn('recruitment_stage', [RecruitmentStage::HR_INTERVIEW, RecruitmentStage::USER_INTERVIEW, RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST, RecruitmentStage::MCU, RecruitmentStage::ONBOARDING, RecruitmentStage::HIRED])
                            ->count(),
                        'interview_scheduled' => Interview::whereHas('application', fn ($q) => $q->whereIn('job_id', $jobIds))
                            ->where('status', 'scheduled')
                            ->count(),
                        'offering_sent' => OfferingLetter::whereHas('application', fn ($q) => $q->whereIn('job_id', $jobIds))
                            ->count(),
                        'hired_candidates' => Application::whereIn('job_id', $jobIds)
                            ->where('recruitment_stage', RecruitmentStage::HIRED)
                            ->count(),
                        'pipeline' => [
                            'applied' => Application::whereIn('job_id', $jobIds)
                                ->where('recruitment_stage', RecruitmentStage::APPLIED)
                                ->count(),
                            'interview' => Application::whereIn('job_id', $jobIds)
                                ->whereIn('recruitment_stage', [RecruitmentStage::HR_INTERVIEW, RecruitmentStage::USER_INTERVIEW])
                                ->count(),
                            'offer' => Application::whereIn('job_id', $jobIds)
                                ->where('recruitment_stage', RecruitmentStage::OFFERING)
                                ->count(),
                            'hired' => Application::whereIn('job_id', $jobIds)
                                ->where('recruitment_stage', RecruitmentStage::HIRED)
                                ->count(),
                        ],
                    ];
                }

                return $stats;
            }
        );

        return view('livewire.dashboard', compact('stats'));
    }

    private function monthlyTrend(): array
    {
        $driver = DB::connection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';

        return Application::selectRaw($monthExpression . ' as month, count(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }
}
