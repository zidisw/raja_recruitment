<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Article;
use App\Models\Job;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ArticleDetail extends Component
{
    public Article $article;

    public function mount(Article $article): void
    {
        abort_unless($article->is_published && $article->published_at <= now(), 404);
        $this->article = $article;
    }

    public function render(): View
    {
        $relatedArticles = Article::query()
            ->published()
            ->with('featuredImage')
            ->where('id', '!=', $this->article->id)
            ->when($this->article->category, fn($q) => $q->where('category', $this->article->category))
            ->latest('published_at')
            ->limit(3)
            ->get();

        $latestJobs = Job::query()
            ->active()
            ->with(['department', 'site'])
            ->latest()
            ->limit(3)
            ->get();

        return view('livewire.public.article-detail', [
            'relatedArticles' => $relatedArticles,
            'latestJobs' => $latestJobs,
        ]);
    }
}
