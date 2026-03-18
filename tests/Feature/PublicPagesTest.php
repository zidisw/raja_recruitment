<?php

use App\Models\Article;
use App\Models\Job;
use App\Models\User;

test('home page returns 200', function () {
    $this->get(route('home'))->assertOk();
});

test('about page returns 200', function () {
    $this->get(route('about'))->assertOk();
});

test('articles index page returns 200', function () {
    $this->get(route('articles.index'))->assertOk();
});

test('articles index shows published articles', function () {
    $author = User::factory()->create();
    $article = Article::create([
        'title' => 'Test Article',
        'slug' => 'test-article',
        'content' => 'Some content here.',
        'author_id' => $author->id,
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('articles.index'))->assertSee('Test Article');
});

test('article detail page returns 200 for published article', function () {
    $author = User::factory()->create();
    $article = Article::create([
        'title' => 'Published Article',
        'slug' => 'published-article',
        'content' => 'Content.',
        'author_id' => $author->id,
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('articles.show', $article))->assertOk();
});

test('article detail returns 404 for unpublished article', function () {
    $author = User::factory()->create();
    $article = Article::create([
        'title' => 'Draft Article',
        'slug' => 'draft-article',
        'content' => 'Content.',
        'author_id' => $author->id,
        'is_published' => false,
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('articles.show', $article))->assertNotFound();
});

test('article detail returns 404 for scheduled article not yet published', function () {
    $author = User::factory()->create();
    $article = Article::create([
        'title' => 'Future Article',
        'slug' => 'future-article',
        'content' => 'Content.',
        'author_id' => $author->id,
        'is_published' => true,
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('articles.show', $article))->assertNotFound();
});

test('careers index page returns 200', function () {
    $this->get(route('careers.index'))->assertOk();
});

test('careers index shows active jobs', function () {
    $creator = User::factory()->create();
    Job::create([
        'title' => 'Heavy Equipment Operator',
        'description' => 'Job description.',
        'requirements' => 'Requirements.',
        'level' => 'staff',
        'is_active' => true,
        'created_by' => $creator->id,
    ]);

    $this->get(route('careers.index'))->assertSee('Heavy Equipment Operator');
});

test('career detail page returns 200 for active job', function () {
    $creator = User::factory()->create();
    $job = Job::create([
        'title' => 'Active Job',
        'description' => 'Description.',
        'requirements' => 'Requirements.',
        'level' => 'staff',
        'is_active' => true,
        'created_by' => $creator->id,
    ]);

    $this->get(route('careers.show', $job))->assertOk();
});

test('career detail returns 404 for inactive job', function () {
    $creator = User::factory()->create();
    $job = Job::create([
        'title' => 'Inactive Job',
        'description' => 'Description.',
        'requirements' => 'Requirements.',
        'level' => 'staff',
        'is_active' => false,
        'created_by' => $creator->id,
    ]);

    $this->get(route('careers.show', $job))->assertNotFound();
});
