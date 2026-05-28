<?php

use App\Enums\UserRole;
use App\Models\Job;
use App\Models\JobImage;
use App\Models\Ptk;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('public');
});

function createPtkForUser(User $user, string $posisi = 'Heavy Equipment Operator'): Ptk
{
    return Ptk::create([
        'nomor_ptk' => 'PTK-'.uniqid(),
        'department' => 'HR',
        'posisi' => $posisi,
        'jumlah_kebutuhan' => 1,
        'alasan_permintaan' => 'Test requirement',
        'tanggal_permintaan' => now()->toDateString(),
        'status' => 'approved',
        'created_by' => $user->id,
    ]);
}

test('guests cannot access job management', function () {
    $this->get(route('jobs.index'))->assertRedirect(route('login'));
});

test('superadmin can access job management', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    actingAs($user);

    $this->get(route('jobs.index'))->assertOk();
});

test('superadmin can create a job posting', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $ptk = createPtkForUser($user);

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('openCreate')
        ->set('title', 'Heavy Equipment Operator')
        ->set('description', 'Operate heavy machinery safely.')
        ->set('requirements', 'Valid SIM B2 license required.')
        ->set('level', 'staff')
        ->set('ptk_id', $ptk->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Job::where('title', 'Heavy Equipment Operator')->exists())->toBeTrue();
});

test('superadmin can upload a featured image when creating job', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $ptk = createPtkForUser($user, 'Job With Image');
    $file = UploadedFile::fake()->image('job-featured.jpg', 800, 600);

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('openCreate')
        ->set('title', 'Job With Image')
        ->set('description', 'Description.')
        ->set('requirements', 'Requirements.')
        ->set('level', 'non_staff')
        ->set('ptk_id', $ptk->id)
        ->set('featuredImage', $file)
        ->call('save')
        ->assertHasNoErrors();

    $job = Job::where('ptk_id', $ptk->id)->latest('id')->first();
    expect($job)->not->toBeNull();
    expect($job->featuredImage)->not->toBeNull();
    Storage::disk('public')->assertExists($job->featuredImage->path);
});

test('superadmin can upload gallery images for job', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $ptk = createPtkForUser($user, 'Job With Gallery');

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('openCreate')
        ->set('title', 'Job With Gallery')
        ->set('description', 'Description.')
        ->set('requirements', 'Requirements.')
        ->set('level', 'staff')
        ->set('ptk_id', $ptk->id)
        ->set('galleryImages', [
            UploadedFile::fake()->image('gallery1.jpg'),
            UploadedFile::fake()->image('gallery2.jpg'),
        ])
        ->call('save')
        ->assertHasNoErrors();

    $job = Job::where('ptk_id', $ptk->id)->latest('id')->first();
    expect($job->images()->where('is_featured', false)->count())->toBe(2);
});

test('superadmin can delete a job image', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $job = Job::create([
        'title' => 'Image Job',
        'description' => 'Description.',
        'requirements' => 'Requirements.',
        'level' => 'staff',
        'is_active' => true,
        'created_by' => $user->id,
    ]);

    $path = 'test-images/fake-image.jpg';
    Storage::disk('public')->put($path, 'fake image content');
    $image = $job->images()->create(['path' => $path, 'is_featured' => true, 'sort_order' => 0]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('deleteImage', $image->id);

    expect(JobImage::find($image->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('deleting a job also deletes its images from storage', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $job = Job::create([
        'title' => 'Job To Delete',
        'description' => 'Description.',
        'requirements' => 'Requirements.',
        'level' => 'staff',
        'is_active' => true,
        'created_by' => $user->id,
    ]);

    $path = 'test-images/fake-image.jpg';
    Storage::disk('public')->put($path, 'fake image content');
    $job->images()->create(['path' => $path, 'is_featured' => true, 'sort_order' => 0]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('delete', $job->id);

    expect(Job::find($job->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('job creation validates required fields', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\JobManagement::class)
        ->call('openCreate')
        ->call('save')
        ->assertHasErrors(['title', 'description', 'requirements', 'level']);
});
