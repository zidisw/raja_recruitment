<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Job;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class CareerDetail extends Component
{
    public Job $job;

    public function mount(Job $job): void
    {
        abort_unless($job->is_active, 404);
        $this->job = $job;
    }

    public function render(): View
    {
        $relatedJobs = Job::query()
            ->active()
            ->with(['department', 'site'])
            ->where('id', '!=', $this->job->id)
            ->where(function ($q) {
                $q->where('department_id', $this->job->department_id)
                    ->orWhere('level', $this->job->level);
            })
            ->limit(4)
            ->get();

        return view('livewire.public.career-detail', [
            'relatedJobs' => $relatedJobs,
        ]);
    }
}
