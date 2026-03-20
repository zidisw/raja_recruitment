<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InterviewManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    public bool $showUploadModal = false;
    public ?int $uploadingInterviewId = null;
    /** @var \Illuminate\Http\UploadedFile|null */
    public $upload_file = null;

    public ?int $application_id = null;
    public ?int $interviewer_id = null;
    public string $interview_type = 'HR Interview';
    public string $scheduled_date = '';
    public string $scheduled_time = '';
    public string $status = 'scheduled';
    public string $hr_notes = '';
    
    /** @var \Illuminate\Http\UploadedFile|null */
    public $evaluation_file = null;

    public string $tab = 'hr';

    public function mount(string $tab = 'hr'): void
    {
        $this->authorizeAccess();

        if (in_array($tab, ['hr', 'user'])) {
            $this->tab = $tab;
        }
    }

    private function authorizeAccess(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->interview_type = $this->tab === 'hr' ? 'HR Interview' : 'User Interview';
        $this->showModal = true;
    }

    public function openScheduleUserInterview(int $applicationId): void
    {
        $this->resetForm();
        $this->application_id = $applicationId;
        $this->interview_type = 'User Interview';
        $this->showModal = true;
    }

    public function openUploadModal(int $interviewId): void
    {
        $this->uploadingInterviewId = $interviewId;
        $this->upload_file = null;
        $this->showUploadModal = true;
    }

    public function saveUpload(): void
    {
        $this->validate([
            'uploadingInterviewId' => ['required', 'exists:interviews,id'],
            'upload_file' => ['required', 'file', 'mimes:pdf,docx,jpg,png,jpeg', 'max:5120'],
        ]);

        $interview = Interview::findOrFail($this->uploadingInterviewId);

        if ($interview->evaluation_path) {
            Storage::disk('public')->delete($interview->evaluation_path);
        }

        $interview->update([
            'evaluation_path' => $this->upload_file->store('interviews/evaluations', 'public'),
        ]);

        $this->showUploadModal = false;
        $this->uploadingInterviewId = null;
        $this->upload_file = null;
        
        $this->dispatch('notify', ['message' => __('Evaluation file uploaded successfully.'), 'type' => 'success']);
    }

    public function exportCsv()
    {
        $interviews = $this->getFilteredQuery()->get();
        
        $csvData = "Nama Kandidat,Posisi,Departemen,Pewawancara,Tanggal Interview,Status\n";
        
        foreach ($interviews as $interview) {
            $name = '"' . str_replace('"', '""', $interview->application->candidate->name) . '"';
            $jobTitle = '"' . str_replace('"', '""', $interview->application->job->title) . '"';
            $dept = '"' . str_replace('"', '""', $interview->application->job->department?->name ?? '') . '"';
            $interviewer = '"' . str_replace('"', '""', $interview->interviewer?->name ?? '—') . '"';
            $date = $interview->scheduled_at ? $interview->scheduled_at->format('Y-m-d H:i') : '';
            $status = $interview->status;
            
            $csvData .= "{$name},{$jobTitle},{$dept},{$interviewer},{$date},{$status}\n";
        }
        
        $filename = "export_interview_{$this->tab}_" . now()->format('Ymd_His') . ".csv";
        
        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $filename);
    }

    public function updateInterviewStatus(int $id, string $newStatus): void
    {
        $this->authorizeAccess();

        $validStatuses = ['scheduled', 'completed', 'passed', 'failed'];
        if (!in_array($newStatus, $validStatuses)) {
            $this->dispatch('notify', ['message' => __('Invalid status selected.'), 'type' => 'error']);
            return;
        }

        $interview = Interview::findOrFail($id);

        // Block 'passed' or 'failed' if evaluation file not uploaded
        if (in_array($newStatus, ['passed', 'failed']) && !$interview->evaluation_path) {
            $this->dispatch('notify', ['message' => __('Upload file penilaian terlebih dahulu sebelum mengubah status.'), 'type' => 'error']);
            return;
        }

        $interview->update(['status' => $newStatus]);

        $this->syncApplicationStage($interview);

        $this->dispatch('notify', ['message' => __('Interview status updated successfully.'), 'type' => 'success']);
    }

    public function openEdit(Interview $interview): void
    {
        $this->editingId = $interview->id;
        $this->application_id = $interview->application_id;
        $this->interviewer_id = $interview->interviewer_id;
        $this->interview_type = $interview->interview_type;
        $this->scheduled_date = $interview->scheduled_at?->format('Y-m-d') ?? '';
        $this->scheduled_time = $interview->scheduled_at?->format('H:i') ?? '';
        $this->status = $interview->status;
        $this->hr_notes = (string) $interview->hr_notes;
        $this->evaluation_file = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'interviewer_id' => [
                'required',
                Rule::exists('users', 'id')->whereIn('role', [
                    UserRole::Admin->value,
                    UserRole::SuperAdmin->value,
                    UserRole::HR->value,
                    UserRole::Interviewer->value,
                ]),
            ],
            'interview_type' => ['required', 'in:HR Interview,User Interview'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required'],
            'status' => ['required', 'in:scheduled,completed,passed,failed'],
            'hr_notes' => ['nullable', 'string', 'max:2000'],
            'evaluation_file' => ['nullable', 'file', 'mimes:pdf,docx', 'max:5120'],
        ]);

        $scheduledAt = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'] . ':00';

        $payload = [
            'application_id' => $validated['application_id'],
            'interviewer_id' => $validated['interviewer_id'],
            'interview_type' => $validated['interview_type'],
            'scheduled_at' => $scheduledAt,
            'status' => $validated['status'],
            'hr_notes' => $validated['hr_notes'] ?? null,
        ];

        if ($this->editingId) {
            $interview = Interview::findOrFail($this->editingId);

            // Block 'passed' or 'failed' without evaluation file (existing or newly uploaded)
            if (in_array($validated['status'], ['passed', 'failed']) && !$interview->evaluation_path && !$this->evaluation_file) {
                $this->dispatch('notify', ['message' => __('Upload file penilaian terlebih dahulu sebelum mengubah status.'), 'type' => 'error']);
                return;
            }

            $interview->update($payload);
        } else {
            // New interview: block 'passed' or 'failed' without evaluation file
            if (in_array($validated['status'], ['passed', 'failed']) && !$this->evaluation_file) {
                $this->dispatch('notify', ['message' => __('Upload file penilaian terlebih dahulu sebelum mengubah status.'), 'type' => 'error']);
                return;
            }

            $interview = Interview::create($payload);
        }

        if ($this->evaluation_file) {
            if ($interview->evaluation_path) {
                Storage::disk('public')->delete($interview->evaluation_path);
            }

            $interview->update([
                'evaluation_path' => $this->evaluation_file->store('interviews/evaluations', 'public'),
            ]);
        }

        $this->syncApplicationStage($interview);

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('notify', ['message' => __('Interview saved successfully.'), 'type' => 'success']);
    }

    private function getFilteredQuery()
    {
        $type = $this->tab === 'hr' ? 'HR Interview' : 'User Interview';
        $targetStage = $this->tab === 'hr' ? RecruitmentStage::HR_INTERVIEW : RecruitmentStage::USER_INTERVIEW;

        return Interview::with(['application.candidate', 'application.job.department', 'interviewer'])
            ->where('interview_type', $type)
            ->whereHas('application', function ($query) use ($targetStage) {
                $query->where('recruitment_stage', $targetStage);
            })
            ->latest('scheduled_at');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.interview-management', [
            'interviews' => $this->getFilteredQuery()->paginate(10),
            'applications' => Application::with(['candidate', 'job', 'interviews'])
                ->whereIn('recruitment_stage', [RecruitmentStage::HR_INTERVIEW, RecruitmentStage::USER_INTERVIEW])
                ->get(),
            'interviewers' => User::whereIn('role', [
                UserRole::Admin,
                UserRole::HR,
                UserRole::Interviewer,
            ])->get(),
        ]);
    }

    private function syncApplicationStage(Interview $interview): void
    {
        $application = $interview->application;

        // Condition: If ANY interview is failed, move to REJECTED
        if ($interview->status === 'failed') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
            return;
        }

        // Fetch the latest interviews for the application
        $hrInterview = $application->interviews()->where('interview_type', 'HR Interview')->latest('scheduled_at')->first();
        $userInterview = $application->interviews()->where('interview_type', 'User Interview')->latest('scheduled_at')->first();

        // Condition 2: Move to OFFERING if User Interview is passed AND has form
        if ($userInterview && $userInterview->status === 'passed' && $userInterview->evaluation_path) {
            if ($application->recruitment_stage === RecruitmentStage::USER_INTERVIEW) {
                $application->update([
                    'recruitment_stage' => RecruitmentStage::OFFERING,
                    'stage_updated_at' => now(),
                ]);
            }
            return;
        }

        // Condition 1: Move from HR_INTERVIEW to USER_INTERVIEW if:
        // HR is passed AND HR has form AND User Interview exists (scheduled)
        if ($hrInterview && $hrInterview->status === 'passed' && $hrInterview->evaluation_path) {
            if ($userInterview) {
                if ($application->recruitment_stage === RecruitmentStage::HR_INTERVIEW) {
                    $application->update([
                        'recruitment_stage' => RecruitmentStage::USER_INTERVIEW,
                        'stage_updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'application_id',
            'interviewer_id',
            'scheduled_date',
            'scheduled_time',
            'evaluation_file',
            'hr_notes',
            'uploadingInterviewId',
            'upload_file',
        ]);

        $this->interview_type = 'HR Interview';
        $this->status = 'scheduled';
    }
}
