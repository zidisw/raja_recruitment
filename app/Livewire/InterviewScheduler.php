<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use App\Models\Application;
use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Interview;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterviewInvitationMail;
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
        if ($this->application->interview) {
            $this->interviewer_id = $this->application->interview->interviewer_id;
            $this->scheduled_date = $this->application->interview->scheduled_at->format('Y-m-d');
            $this->scheduled_time = $this->application->interview->scheduled_at->format('H:i');
            $this->meeting_link = $this->application->interview->meeting_link;
        }
    }

    public function openScheduler()
    {
        if (!$this->isValidInterviewStatus()) {
            $this->dispatch('notify', message: __('You can only schedule an interview when the candidate is in the Interview stage.'), type: 'error');
            return;
        }
        $this->showModal = true;
    }

    private function isValidInterviewStatus(): bool
    {
        return in_array($this->application->recruitment_stage, [
            RecruitmentStage::HR_INTERVIEW,
            RecruitmentStage::USER_INTERVIEW,
        ]);
    }

    public function saveSchedule()
    {
        if (!$this->isValidInterviewStatus()) {
            $this->dispatch('notify', message: __('Cannot schedule an interview for the current application stage.'), type: 'error');
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

        $scheduledAt = $this->scheduled_date . ' ' . $this->scheduled_time . ':00';

        $interview = Interview::updateOrCreate(
            ['application_id' => $this->application->id],
            [
                'interviewer_id' => $this->interviewer_id,
                'scheduled_at' => $scheduledAt,
                'meeting_link' => $this->meeting_link,
                'status' => 'scheduled'
            ]
        );

        // Send Email to Candidate and Interviewer
        try {
            Mail::to($this->application->candidate->email)->send(
                new InterviewInvitationMail($interview, 'candidate')
            );
            Mail::to($interview->interviewer->email)->send(
                new InterviewInvitationMail($interview, 'interviewer')
            );
            $this->dispatch('notify', message: __('Interview scheduled and invitation emails sent!'), type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('notify', message: __('Interview scheduled, but failed to send email.'), type: 'error');
        }

        $this->application->refresh();
        $this->showModal = false;
        $this->dispatch('interview-scheduled');
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
