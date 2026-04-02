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
                                    : (RecruitmentStage::tryFrom($item->recruitment_stage ?? '')?->label() ?? 'Unknown'),
                                'data' => [(int) ($item->total ?? 0)],
                            ])
                            ->values(),

                        'monthly_trend' => $this->monthlyTrend(),
                    ];
                } elseif ($user->hasAdminRole()) {
                    $stats = [
                        'total_open_jobs' => Job::where('is_active', true)->count(),
                        'total_applicants' => Application::count(),
                        'administrative_passed' => Application::query()
                            ->whereIn('recruitment_stage', [RecruitmentStage::ADMINISTRASI, RecruitmentStage::HR_INTERVIEW, RecruitmentStage::USER_INTERVIEW, RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST, RecruitmentStage::MCU, RecruitmentStage::ONBOARDING, RecruitmentStage::HIRED])
                            ->count(),
                        'interview_scheduled' => Interview::query()
                            ->where('status', 'scheduled')
                            ->count(),
                        'offering_sent' => OfferingLetter::count(),
                        'hired_candidates' => Application::query()
                            ->where('recruitment_stage', RecruitmentStage::HIRED)
                            ->count(),
                        'pipeline' => [
                            'applied' => Application::query()
                                ->where('recruitment_stage', RecruitmentStage::APPLIED)
                                ->count(),
                            'interview' => Application::query()
                                ->whereIn('recruitment_stage', [RecruitmentStage::HR_INTERVIEW, RecruitmentStage::USER_INTERVIEW])
                                ->count(),
                            'offer' => Application::query()
                                ->where('recruitment_stage', RecruitmentStage::OFFERING)
                                ->count(),
                            'hired' => Application::query()
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
