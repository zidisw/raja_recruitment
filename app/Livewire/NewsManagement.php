<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NewsManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;

    public $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $category = '';

    public string $content = '';

    public bool $is_published = false;

    public string $published_at = '';

    public ?TemporaryUploadedFile $featuredImage = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $galleryImages = [];

    #[Computed]
    public function existingFeaturedImage(): ?ArticleImage
    {
        if (! $this->editingId) {
            return null;
        }

        return Article::find($this->editingId)?->featuredImage;
    }

    #[Computed]
    public function existingGalleryImages()
    {
        if (! $this->editingId) {
            return collect();
        }

        return Article::find($this->editingId)?->images->where('is_featured', false) ?? collect();
    }

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->canAccessRecruitment(), 403);
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->published_at = now()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function openEdit(int $articleId): void
    {
        $article = Article::findOrFail($articleId);

        $this->resetForm();
        $this->editingId = $article->id;
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->category = $article->category ?? '';
        $this->content = $article->content;
        $this->is_published = $article->is_published;
        $this->published_at = $article->published_at
            ? $article->published_at->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($this->editingId),
            ],
            'category' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'featuredImage' => ['nullable', 'image', 'max:5120'],
            'galleryImages.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category ?: null,
            'content' => $this->content,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at ?: now(),
        ];

        if ($this->editingId) {
            $article = Article::findOrFail($this->editingId);
            $article->update($data);
        } else {
            $data['author_id'] = Auth::id();
            $article = Article::create($data);
        }

        // Handle featured image upload
        if ($this->featuredImage) {
            $existing = $article->images()->where('is_featured', true)->first();
            if ($existing) {
                Storage::disk('public')->delete($existing->path);
                $existing->delete();
            }
            $path = $this->featuredImage->store('articles', 'public');
            $article->images()->create([
                'path' => $path,
                'is_featured' => true,
                'sort_order' => 0,
            ]);
        }

        // Handle gallery images upload
        if (! empty($this->galleryImages)) {
            $maxOrder = $article->images()->where('is_featured', false)->max('sort_order') ?? 0;
            foreach ($this->galleryImages as $index => $image) {
                $path = $image->store('articles', 'public');
                $article->images()->create([
                    'path' => $path,
                    'is_featured' => false,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }

        $this->dispatch('notify', ['message' => $this->editingId ? 'Article updated successfully.' : 'Article published successfully.', 'type' => 'success']);
        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteImage(int $imageId): void
    {
        $image = ArticleImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    public function delete(int $articleId): void
    {
        $article = Article::with('images')->findOrFail($articleId);

        foreach ($article->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $article->delete();
        $this->dispatch('notify', ['message' => 'Article deleted successfully.', 'type' => 'success']);
    }

    public function render(): View
    {
        return view('livewire.news-management', [
            'articles' => Article::with(['author', 'featuredImage'])
                ->latest()
                ->paginate(15),
            'categories' => ['CSR Program', 'Achievement', 'Operations', 'Company News', 'Other'],
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'slug', 'category', 'content',
            'is_published', 'published_at', 'featuredImage', 'galleryImages',
        ]);
    }
}
