<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Article;
use App\Models\Department;
use App\Models\Job;
use App\Models\Site;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.public')]
class CareerList extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $department = '';

    #[Url]
    public string $site = '';

    #[Url]
    public string $level = '';

    public function render(): View
    {
        $query = Job::query()
            ->active()
            ->with(['department', 'site', 'featuredImage'])
            ->latest();

        if ($this->search !== '') {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->department !== '') {
            $query->where('department_id', $this->department);
        }

        if ($this->site !== '') {
            $query->where('site_id', $this->site);
        }

        if ($this->level !== '') {
            $query->where('level', $this->level);
        }

        $latestNews = Article::query()
            ->published()
            ->with('featuredImage')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('livewire.public.career-list', [
            'jobs' => $query->get(),
            'departments' => Department::orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'latestNews' => $latestNews,
        ]);
    }
}
