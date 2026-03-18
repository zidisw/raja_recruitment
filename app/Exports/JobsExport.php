<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JobsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $search = '',
        private readonly string $filterDepartment = '',
        private readonly string $filterSite = '',
        private readonly string $filterLevel = '',
        private readonly string $filterStatus = '',
        private readonly ?int $departmentId = null,
    ) {}

    public function query(): Builder
    {
        $query = Job::with(['department', 'site'])
            ->withCount('applications')
            ->latest();

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->filterSite) {
            $query->where('site_id', $this->filterSite);
        }

        if ($this->filterLevel) {
            $query->where('level', $this->filterLevel);
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Department',
            'Site',
            'Level',
            'Status',
            'Applicants',
            'Closing Date',
            'Created At',
        ];
    }

    /** @param Job $row */
    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->department?->name ?? '—',
            $row->site?->name ?? '—',
            $row->level->name,
            $row->is_active ? 'Active' : 'Inactive',
            $row->applications_count,
            $row->closed_at?->format('d M Y') ?? '—',
            $row->created_at->format('d M Y'),
        ];
    }
}
