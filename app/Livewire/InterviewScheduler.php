<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\Interview;
use App\Models\User;
use App\Services\RecruitmentNotificationService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class InterviewScheduler extends Component
{
    public Application $application;

    public $interviewer_id;

    public $scheduled_date;

    public $scheduled_time;

    public $meeting_link;

    public bool $showModal = false;

    public function mount(Application $application)
    {
        $this->application = $application;
        $interview = $this->currentInterview();

        if ($interview) {
            $this->interviewer_id = $interview->interviewer_id;
            $this->scheduled_date = $interview->scheduled_at->format('Y-m-d');
            $this->scheduled_time = $interview->scheduled_at->format('H:i');
            $this->meeting_link = $interview->meeting_link;
        }
    }

    public function openScheduler()
    {
        if (! $this->isValidInterviewStatus()) {
            $this->dispatch('notify', ['message' => __('You can only schedule an interview when the candidate is in the Interview stage.'), 'type' => 'error']);

            return;
        }
        $this->showModal = true;
    }

    private function isValidInterviewStatus(): bool
    {
        return in_array($this->application->recruitment_stage, [
            RecruitmentStage::ADMINISTRASI,
            RecruitmentStage::HR_INTERVIEW,
            RecruitmentStage::USER_INTERVIEW,
        ], true);
    }

    public function saveSchedule()
    {
        if (! $this->isValidInterviewStatus()) {
            $this->dispatch('notify', ['message' => __('Cannot schedule an interview for the current application stage.'), 'type' => 'error']);

            return;
        }

        $this->validate([
            'interviewer_id' => [
                'required',
                Rule::exists('users', 'id')->whereIn('role', [
                    UserRole::Admin->value,
                    UserRole::SuperAdmin->value,
                    UserRole::HR->value,
                    UserRole::Interviewer->value,
                ]),
            ],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required'],
            'meeting_link' => ['nullable', 'url'],
        ]);

        $scheduledAt = $this->scheduled_date.' '.$this->scheduled_time.':00';
        $interviewType = $this->targetInterviewType();

        $interview = Interview::updateOrCreate(
            [
                'application_id' => $this->application->id,
                'interview_type' => $interviewType,
            ],
            [
                'interview_type' => $interviewType,
                'interviewer_id' => $this->interviewer_id,
                'scheduled_at' => $scheduledAt,
                'meeting_link' => $this->meeting_link,
                'status' => 'scheduled',
            ]
        );

        if ($interviewType === 'HR Interview' && $this->application->recruitment_stage !== RecruitmentStage::HR_INTERVIEW) {
            $oldStage = $this->application->recruitment_stage;
            $this->application->update([
                'recruitment_stage' => RecruitmentStage::HR_INTERVIEW,
                'stage_updated_at' => now(),
            ]);

            ApplicationStageLog::create([
                'application_id' => $this->application->id,
                'stage' => $oldStage->value,
                'decision' => 'passed',
                'notes' => 'HR Interview dijadwalkan',
                'decided_by' => auth()->id() ?? $this->application->user_id,
            ]);
        }

        $this->notifyInterviewScheduled($interview);

        $this->application->refresh();
        $this->showModal = false;
        $this->dispatch('interview-scheduled');
        $this->dispatch('notify', ['message' => __('Interview scheduled and invitations sent.'), 'type' => 'success']);
    }

    private function currentInterview(): ?Interview
    {
        return $this->application->interviews()
            ->where('interview_type', $this->targetInterviewType())
            ->first();
    }

    private function targetInterviewType(): string
    {
        return $this->application->recruitment_stage === RecruitmentStage::USER_INTERVIEW
            ? 'User Interview'
            : 'HR Interview';
    }

    private function notifyInterviewScheduled(Interview $interview): void
    {
        $interview->loadMissing(['application.candidate', 'application.job', 'interviewer']);
        $application = $interview->application;
        $type = $interview->interview_type === 'User Interview' ? __('Interview User') : __('Interview HR');
        $route = $interview->interview_type === 'User Interview' ? route('interviews.user') : route('interviews.hr');

        app(RecruitmentNotificationService::class)->notifyDatabaseAndMail(
            $application->candidate,
            __('Jadwal :type', ['type' => $type]),
            __(':type untuk posisi :job dijadwalkan pada :date.', [
                'type' => $type,
                'job' => $application->job->title,
                'date' => $interview->scheduled_at?->format('d M Y H:i'),
            ]),
            route('candidate.applications'),
            'interview_scheduled'
        );

        if ($interview->interviewer) {
            app(RecruitmentNotificationService::class)->notifyDatabaseAndMail(
                $interview->interviewer,
                __('Jadwal :type', ['type' => $type]),
                __('Anda ditugaskan mewawancarai :candidate untuk posisi :job pada :date.', [
                    'candidate' => $application->candidate->name,
                    'job' => $application->job->title,
                    'date' => $interview->scheduled_at?->format('d M Y H:i'),
                ]),
                $route,
                'interview_scheduled'
            );
        }
    }

    public function render()
    {
        $interviewers = User::whereIn('role', [
            UserRole::Admin,
            UserRole::HR,
            UserRole::Interviewer,
        ])->get();

        return view('livewire.interview-scheduler', compact('interviewers'));
    }
}
