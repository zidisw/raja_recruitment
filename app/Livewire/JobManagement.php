<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\JobLevel;
use App\Exports\JobsExport;
use App\Models\Department;
use App\Models\Job;
use App\Models\JobImage;
use App\Models\Ptk;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class JobManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $searchTitle = '';

    public string $filterDepartment = '';

    public string $filterSite = '';

    public string $filterLevel = '';

    public string $filterStatus = '';

    public int $perPage = 10;

    public bool $showModal = false;

    public $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $requirements = '';

    public string $benefits = '';

    public string $level = '';

    public bool $is_active = true;

    public string $closed_at = '';

    public $department_id = null;

    public $site_id = null;

    public $ptk_id = null;

    public $featuredImage = null;

    public array $galleryImages = [];

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->canAccessRecruitment(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'description', 'requirements', 'benefits', 'level', 'closed_at', 'site_id', 'department_id', 'ptk_id', 'featuredImage', 'galleryImages']);
        $this->is_active = true;

        $this->showModal = true;
    }

    public function openEdit(int $jobId): void
    {
        $job = Job::with('ptk')->findOrFail($jobId);

        $this->reset(['featuredImage', 'galleryImages']);
        $this->editingId = $job->id;
        $this->title = $job->ptk?->posisi ?? $job->title;
        $this->description = $job->description;
        $this->requirements = $job->requirements;
        $this->benefits = (string) ($job->benefits ?? '');
        $this->level = $job->level->value;
        $this->is_active = $job->is_active;
        $this->closed_at = $job->closed_at ? $job->closed_at->format('Y-m-d') : '';
        $this->department_id = $job->department_id;
        $this->site_id = $job->site_id;
        $this->ptk_id = $job->ptk_id;
        $this->showModal = true;
    }

    public function updatedPtkId($value): void
    {
        if (! $value) {
            $this->title = '';

            return;
        }

        $ptk = Ptk::find($value);
        $this->title = (string) ($ptk?->posisi ?? '');
    }

    public function save(): void
    {
        $user = Auth::user();

        if ($this->ptk_id) {
            $ptk = Ptk::find($this->ptk_id);
            $this->title = (string) ($ptk?->posisi ?? '');
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
            'benefits' => ['nullable', 'string'],
            'level' => ['required', 'string', 'in:staff,non_staff'],
            'is_active' => ['boolean'],
            'closed_at' => ['nullable', 'date', 'after:today'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'ptk_id' => ['required', 'exists:ptk,id'],
            'featuredImage' => ['nullable', 'image', 'max:5120'],
            'galleryImages.*' => ['nullable', 'image', 'max:5120'],
        ];

        $validated = $this->validate($rules);

        if (empty($validated['closed_at'])) {
            $validated['closed_at'] = null;
        }

        if ($this->editingId) {
            $job = Job::findOrFail($this->editingId);
            $job->update($validated);
            $this->dispatch('notify', ['message' => 'Job posting updated successfully.', 'type' => 'success']);
        } else {
            $validated['created_by'] = $user->id;
            $job = Job::create($validated);
            $this->dispatch('notify', ['message' => 'Job posting created successfully.', 'type' => 'success']);
        }

        if ($this->featuredImage) {
            $existing = $job->images()->where('is_featured', true)->first();
            if ($existing instanceof JobImage) {
                Storage::disk('public')->delete($existing->path);
                $existing->delete();
            }
            $path = $this->featuredImage->store('jobs', 'public');
            $job->images()->create(['path' => $path, 'is_featured' => true, 'sort_order' => 0]);
        }

        if (! empty($this->galleryImages)) {
            $maxOrder = $job->images()->where('is_featured', false)->max('sort_order') ?? 0;
            foreach ($this->galleryImages as $index => $image) {
                $path = $image->store('jobs', 'public');
                $job->images()->create(['path' => $path, 'is_featured' => false, 'sort_order' => $maxOrder + $index + 1]);
            }
        }

        $this->showModal = false;
        $this->reset(['editingId', 'title', 'description', 'requirements', 'benefits', 'level', 'closed_at', 'site_id', 'department_id', 'ptk_id', 'featuredImage', 'galleryImages']);
        $this->is_active = true;
    }

    public function deleteImage(int $imageId): void
    {
        $image = JobImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    public function toggleActive(int $jobId): void
    {
        $job = Job::findOrFail($jobId);

        $job->update(['is_active' => ! $job->is_active]);
    }

    public function delete(int $jobId): void
    {
        $job = Job::with('images')->findOrFail($jobId);

        foreach ($job->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $job->delete();
        $this->dispatch('notify', ['message' => 'Job posting deleted successfully.', 'type' => 'success']);
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();

        return Excel::download(
            new JobsExport(
                search: $this->searchTitle,
                filterDepartment: $this->filterDepartment,
                filterSite: $this->filterSite,
                filterLevel: $this->filterLevel,
                filterStatus: $this->filterStatus,
                departmentId: null,
            ),
            'jobs-export-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function updatingSearchTitle(): void
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

    public function updatingFilterLevel(): void
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

    public function render(): \Illuminate\View\View
    {
        $user = Auth::user();

        $query = Job::with(['department', 'site', 'ptk', 'creator', 'featuredImage'])
            ->withCount('applications')
            ->latest();

        if ($this->searchTitle) {
            $query->where('title', 'like', "%{$this->searchTitle}%");
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

        return view('livewire.job-management', [
            'jobs' => $query->paginate($this->perPage),
            'departments' => Department::orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'ptkList' => Ptk::whereIn('status', ['approved', 'draft'])->orderByDesc('tanggal_permintaan')->get(),
            'levels' => JobLevel::cases(),
            'isHR' => false,
        ]);
    }
}
