<?php

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('public');
});

test('guests cannot access news management', function () {
    $this->get(route('news.index'))->assertRedirect(route('login'));
});

test('superadmin can access news management', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    actingAs($user);

    $this->get(route('news.index'))->assertOk();
});

test('superadmin can create an article', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openCreate')
        ->set('title', 'New Company Article')
        ->set('slug', 'new-company-article')
        ->set('content', 'Article body content here.')
        ->set('is_published', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(Article::where('slug', 'new-company-article')->exists())->toBeTrue();
});

test('article slug is auto-generated from title when creating', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openCreate')
        ->set('title', 'My Test Article')
        ->assertSet('slug', 'my-test-article');
});

test('superadmin can edit an article', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $article = Article::create([
        'title' => 'Original Title',
        'slug' => 'original-title',
        'content' => 'Original content.',
        'author_id' => $user->id,
        'is_published' => false,
        'published_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openEdit', $article->id)
        ->set('title', 'Updated Title')
        ->set('slug', 'updated-title')
        ->call('save')
        ->assertHasNoErrors();

    expect($article->fresh()->title)->toBe('Updated Title');
});

test('superadmin can delete an article', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $article = Article::create([
        'title' => 'To Delete',
        'slug' => 'to-delete',
        'content' => 'Content.',
        'author_id' => $user->id,
        'is_published' => false,
        'published_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('delete', $article->id);

    expect(Article::find($article->id))->toBeNull();
});

test('superadmin can upload a featured image when creating article', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $file = UploadedFile::fake()->image('featured.jpg', 800, 600);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openCreate')
        ->set('title', 'Article With Image')
        ->set('slug', 'article-with-image')
        ->set('content', 'Content.')
        ->set('featuredImage', $file)
        ->call('save')
        ->assertHasNoErrors();

    $article = Article::where('slug', 'article-with-image')->first();
    expect($article)->not->toBeNull();
    expect($article->featuredImage)->not->toBeNull();
    Storage::disk('public')->assertExists($article->featuredImage->path);
});

test('superadmin can upload gallery images', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openCreate')
        ->set('title', 'Gallery Article')
        ->set('slug', 'gallery-article')
        ->set('content', 'Content.')
        ->set('galleryImages', [
            UploadedFile::fake()->image('gallery1.jpg'),
            UploadedFile::fake()->image('gallery2.jpg'),
        ])
        ->call('save')
        ->assertHasNoErrors();

    $article = Article::where('slug', 'gallery-article')->first();
    expect($article->images()->where('is_featured', false)->count())->toBe(2);
});

test('superadmin can delete an article image', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $article = Article::create([
        'title' => 'Image Article',
        'slug' => 'image-article',
        'content' => 'Content.',
        'author_id' => $user->id,
        'is_published' => false,
        'published_at' => now(),
    ]);

    $path = 'articles/fake-image.jpg';
    Storage::disk('public')->put($path, 'fake image content');
    $image = $article->images()->create(['path' => $path, 'is_featured' => true, 'sort_order' => 0]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('deleteImage', $image->id);

    expect(ArticleImage::find($image->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('article creation validates required fields', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\NewsManagement::class)
        ->call('openCreate')
        ->call('save')
        ->assertHasErrors(['title', 'slug', 'content']);
});
