<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\OfferingStatus;
use App\Enums\RecruitmentStage;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\Department;
use App\Models\OfferingLetter;
use App\Models\Site;
use App\Services\RecruitmentNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OfferingLetterManagement extends Component
{
    use WithFileUploads, WithPagination;

    public bool $showModal = false;

    public ?int $expandedRow = null;

    public ?int $editingId = null;

    public ?int $application_id = null;

    public string $offer_date = '';

    public $offer_file; // For uploading new file

    public string $status = 'waiting_response';

    public string $search = '';

    public string $filterDepartment = '';

    public string $filterSite = '';

    public string $filterStatus = '';

    public int $perPage = 10;

    #[Computed]
    public function currentOfferingLetter(): ?OfferingLetter
    {
        if (! $this->editingId) {
            return null;
        }

        return OfferingLetter::find($this->editingId);
    }

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDepartment(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSite(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(int $applicationId): void
    {
        $this->expandedRow = $this->expandedRow === $applicationId ? null : $applicationId;
    }

    public function openCreate(?int $appId = null): void
    {
        $this->resetForm();
        if ($appId) {
            $this->application_id = $appId;
        }
        $this->showModal = true;
    }

    public function openEdit(int $offeringId): void
    {
        $offering = OfferingLetter::findOrFail($offeringId);

        $this->editingId = $offering->id;
        $this->application_id = $offering->application_id;
        $this->offer_date = $offering->offer_date ? Carbon::parse($offering->offer_date)->format('Y-m-d') : '';
        $this->status = $offering->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'application_id' => ['required', 'exists:applications,id'],
            'offer_date' => ['required', 'date'],
            'status' => ['required', 'in:waiting_response,signed,accepted,rejected'],
            'offer_file' => [$this->editingId ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:5120'],
        ];

        $validated = $this->validate($rules);
        $existingOffering = OfferingLetter::where('application_id', $validated['application_id'])->first();

        if (
            $validated['status'] === OfferingStatus::ACCEPTED->value
            && ! $existingOffering?->signed_file_path
        ) {
            $this->dispatch('notify', ['message' => __('Kandidat harus mengunggah OL tertandatangan sebelum admin memvalidasi penawaran.'), 'type' => 'error']);

            return;
        }

        $data = [
            'application_id' => $validated['application_id'],
            'offer_date' => $validated['offer_date'],
            'status' => $validated['status'],
        ];

        if ($this->offer_file) {
            $data['file_path'] = $this->offer_file->store('offering-letters', 'public');
        }

        $offering = OfferingLetter::updateOrCreate(
            ['application_id' => $validated['application_id']],
            $data
        );

        $application = $offering->application;
        $oldStage = $application->recruitment_stage;

        // Sync Application Stage based on Offering Status
        if ($validated['status'] === OfferingStatus::ACCEPTED->value) {
            $targetStage = RecruitmentStage::PSYCHOTEST;
        } elseif ($validated['status'] === OfferingStatus::REJECTED->value) {
            $targetStage = RecruitmentStage::REJECTED;
        } else {
            $targetStage = RecruitmentStage::OFFERING;
        }

        if ($oldStage !== $targetStage) {
            $application->update([
                'recruitment_stage' => $targetStage,
                'stage_updated_at' => now(),
            ]);

            ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $oldStage->value,
                'decision' => $targetStage === RecruitmentStage::REJECTED ? 'rejected' : 'passed',
                'notes' => 'Offering status: '.str_replace('_', ' ', $validated['status']),
                'decided_by' => Auth::id() ?? $application->user_id,
            ]);
        }

        if ($offering->wasRecentlyCreated || $this->offer_file) {
            app(RecruitmentNotificationService::class)->notifyDatabaseAndMail(
                $application->candidate,
                __('Offering Letter Tersedia'),
                __('Offering Letter untuk posisi :job sudah tersedia. Silakan buka portal kandidat untuk membaca dan mengunggah dokumen yang sudah ditandatangani.', [
                    'job' => $application->job->title,
                ]),
                route('candidate.applications'),
                'offering_letter_sent'
            );
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('notify', ['message' => __('Offering letter saved successfully.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = Application::with(['candidate', 'job.department', 'job.site', 'offeringLetter'])
            ->where(function ($q): void {
                $q->whereIn('recruitment_stage', [RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST])
                    ->orWhereHas('offeringLetter');
            })
            ->latest('updated_at');

        if ($this->search !== '') {
            $query->where(function ($q): void {
                $q->whereHas('candidate', function ($candidate): void {
                    $candidate->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })->orWhereHas('job', function ($job): void {
                    $job->where('title', 'like', '%'.$this->search.'%');
                });
            });
        }

        if ($this->filterDepartment !== '') {
            $query->whereHas('job', fn ($q) => $q->where('department_id', (int) $this->filterDepartment));
        }

        if ($this->filterSite !== '') {
            $query->whereHas('job', fn ($q) => $q->where('site_id', (int) $this->filterSite));
        }

        if ($this->filterStatus !== '') {
            if ($this->filterStatus === 'none') {
                $query->whereDoesntHave('offeringLetter');
            } else {
                $query->whereHas('offeringLetter', fn ($q) => $q->where('status', $this->filterStatus));
            }
        }

        return view('livewire.offering-letter-management', [
            'applications_paginated' => $query->paginate($this->perPage),
            'applications' => Application::with(['candidate', 'job'])
                ->where(function ($query): void {
                    $query->whereIn('recruitment_stage', [RecruitmentStage::OFFERING, RecruitmentStage::PSYCHOTEST])
                        ->orWhereHas('offeringLetter');
                })
                ->get(),
            'statuses' => OfferingStatus::cases(),
            'departments' => Cache::remember('ref.departments', 300, fn () => Department::query()->orderBy('name')->get(['id', 'name'])),
            'sites' => Cache::remember('ref.sites', 300, fn () => Site::query()->orderBy('name')->get(['id', 'name'])),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'application_id', 'offer_date', 'offer_file']);
        $this->status = 'waiting_response';
    }
}
