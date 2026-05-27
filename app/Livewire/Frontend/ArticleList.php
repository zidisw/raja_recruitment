<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Article;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class ArticleList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Article::query()
            ->published()
            ->with(['featuredImage', 'author'])
            ->latest('published_at');

        if ($this->search !== '') {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->category !== '') {
            $query->where('category', $this->category);
        }

        $articles = $query->paginate(9);

        $heroArticle = Article::query()
            ->published()
            ->with(['featuredImage', 'author'])
            ->latest('published_at')
            ->first();

        $categories = Article::query()
            ->published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('livewire.public.article-list', [
            'articles' => $articles,
            'heroArticle' => $heroArticle,
            'categories' => $categories,
        ]);
    }
}
