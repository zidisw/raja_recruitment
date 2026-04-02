<?php

declare(strict_types=1);

namespace App\Exports;

use App\Enums\RecruitmentStage;
use App\Models\Application;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicationsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly int $jobId,
        private readonly string $search = '',
        private readonly array $statusFilter = [],
        private readonly array $genderFilter = [],
        private readonly array $religionFilter = [],
        private readonly array $degreeFilter = [],
        private readonly string $hasExperience = '',
        private readonly string $hasOrganization = '',
        private readonly array $documentsFilter = [],
    ) {}

    public function query(): Builder
    {
        $query = Application::with([
            'candidate.profile',
            'candidate.education',
            'candidate.experiences',
            'candidate.organizations',
            'job',
        ])
            ->where('job_id', $this->jobId)
            ->latest();

        if ($this->search) {
            $query->whereHas('candidate', function ($q): void {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->whereIn('recruitment_stage', $this->statusFilter);
        }

        if ($this->genderFilter) {
            $query->whereHas('candidate.profile', fn($q) => $q->whereIn('gender', $this->genderFilter));
        }

        if ($this->religionFilter) {
            $query->whereHas('candidate.profile', fn($q) => $q->whereIn('religion', $this->religionFilter));
        }

        if ($this->degreeFilter) {
            $query->whereHas('candidate.education', fn($q) => $q->whereIn('degree', $this->degreeFilter));
        }

        if ($this->hasExperience === 'yes') {
            $query->whereHas('candidate.experiences');
        } elseif ($this->hasExperience === 'no') {
            $query->whereDoesntHave('candidate.experiences');
        }

        if ($this->hasOrganization === 'yes') {
            $query->whereHas('candidate.organizations');
        } elseif ($this->hasOrganization === 'no') {
            $query->whereDoesntHave('candidate.organizations');
        }

        foreach ($this->documentsFilter as $doc) {
            $query->whereHas('candidate.profile', fn($q) => $q->whereNotNull("{$doc}_path"));
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Candidate Name',
            'Email',
            'WhatsApp',
            'NIK',
            'Gender',
            'Date of Birth',
            'Age',
            'Religion',
            'Marital Status',
            'Education (Latest Degree)',
            'Institution',
            'Has Work Experience',
            'Has Organization',
            'Documents Uploaded',
            'Status',
            'Applied Date',
        ];
    }

    /** @param Application $row */
    public function map($row): array
    {
        $profile = $row->candidate->profile;
        $latestEdu = $row->candidate->education->sortByDesc('end_year')->first();

        $docs = collect([
            $profile?->ktp_path ? 'KTP' : null,
            $profile?->portfolio_path ? 'Portfolio' : null,
            $profile?->certificate_path ? 'Certificate' : null,
            $profile?->paklaring_path ? 'Paklaring' : null,
        ])->filter()->implode(', ');

        return [
            $row->id,
            $row->candidate->name,
            $row->candidate->email,
            $profile?->whatsapp ?? '—',
            $profile?->nik ?? '—',
            $profile?->gender ?? '—',
            $profile?->date_of_birth?->format('d M Y') ?? '—',
            $profile?->date_of_birth ? now()->diffInYears($profile->date_of_birth) : '—',
            $profile?->religion ?? '—',
            $profile?->marital_status ?? '—',
            $latestEdu?->degree ?? '—',
            $latestEdu?->institution_name ?? '—',
            $row->candidate->experiences->isNotEmpty() ? 'Yes' : 'No',
            $row->candidate->organizations->isNotEmpty() ? 'Yes' : 'No',
            $docs ?: 'None',
            $row->recruitment_stage->label(),
            $row->created_at->format('d M Y'),
        ];
    }
}
