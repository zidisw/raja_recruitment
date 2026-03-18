<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Job;
use App\Models\Site;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class JobApplications extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterDepartment = '';

    public string $filterSite = '';

    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()->canAccessRecruitment(), 403);
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

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();

        $query = Job::with(['department', 'site'])
            ->withCount('applications')
            ->latest();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->filterSite) {
            $query->where('site_id', $this->filterSite);
        }

        return view('livewire.job-applications', [
            'jobs' => $query->paginate($this->perPage),
            'departments' => Department::orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'isHR' => false,
        ]);
    }
}
