<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\ApplicationManagement;
use App\Livewire\CandidateManagement;
use App\Livewire\CandidateReview;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Department;
use App\Models\Job;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->makeCandidateReviewFixture = function (RecruitmentStage $stage = RecruitmentStage::APPLIED): array {
        $department = Department::factory()->create(['name' => 'Detail Route Department']);
        $site = Site::factory()->create(['name' => 'Detail Route Site']);

        $admin = User::factory()->create([
            'name' => 'Detail Route Admin',
            'role' => UserRole::Admin,
            'department_id' => $department->id,
        ]);

        $candidate = User::factory()->create([
            'name' => 'Detail Route Candidate',
            'email' => 'detail.route.candidate@example.com',
            'role' => UserRole::User,
        ]);

        CandidateProfile::create([
            'user_id' => $candidate->id,
            'nik' => '7300000000000991',
            'place_of_birth' => 'Makassar',
            'date_of_birth' => now()->subYears(25)->toDateString(),
            'gender' => 'male',
            'religion' => 'Islam',
            'marital_status' => 'single',
            'address_ktp' => 'Jl. Detail KTP',
            'address_domicile' => 'Jl. Detail Domicile',
            'whatsapp' => '081234567891',
            'linkedin_url' => null,
            'ktp_path' => 'candidate/detail/ktp.pdf',
            'portfolio_path' => 'candidate/detail/portfolio.pdf',
            'certificate_path' => 'candidate/detail/certificate.pdf',
        ]);

        $job = Job::create([
            'title' => 'Detail Route Operator',
            'description' => 'Detail route job description',
            'requirements' => 'Detail route requirements',
            'benefits' => 'Detail route benefits',
            'level' => 'staff',
            'is_active' => true,
            'closed_at' => now()->addMonth()->toDateString(),
            'created_by' => $admin->id,
            'department_id' => $department->id,
            'site_id' => $site->id,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'job_id' => $job->id,
            'recruitment_stage' => $stage,
            'stage_updated_at' => now(),
        ]);

        return [$admin, $candidate, $job, $application, $department, $site];
    };
});

test('candidate detail page opens for a valid job and application pair', function (): void {
    [$admin, $candidate, $job, $application] = ($this->makeCandidateReviewFixture)();

    $this->actingAs($admin)
        ->get(route('applications.review', [$job, $application]))
        ->assertOk()
        ->assertSee($candidate->name)
        ->assertSee($job->title);
});

test('candidate detail keeps rejecting a mismatched job and application pair', function (): void {
    [$admin, , , $application, $department, $site] = ($this->makeCandidateReviewFixture)();

    $otherJob = Job::create([
        'title' => 'Unrelated Detail Route Job',
        'description' => 'Another job description',
        'requirements' => 'Another requirements',
        'benefits' => 'Another benefits',
        'level' => 'staff',
        'is_active' => true,
        'closed_at' => now()->addMonth()->toDateString(),
        'created_by' => $admin->id,
        'department_id' => $department->id,
        'site_id' => $site->id,
    ]);

    $this->actingAs($admin)
        ->get(route('applications.review', [$otherJob, $application]))
        ->assertNotFound();
});

test('candidate detail accepts matching ids when the database returns foreign keys as strings', function (): void {
    [$admin, , $job, $application] = ($this->makeCandidateReviewFixture)();

    $application->setRawAttributes(array_replace($application->getAttributes(), [
        'job_id' => (string) $job->id,
    ]), true);

    $this->actingAs($admin);

    $component = new CandidateReview;
    $component->mount($job, $application);

    expect($component->application->job_id)->toBe($job->id);
});

test('candidate tabs render reachable detail links', function (string $tab, RecruitmentStage $stage): void {
    [$admin, , $job, $application] = ($this->makeCandidateReviewFixture)($stage);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => $tab])
        ->assertSee('/applications/'.$job->id.'/'.$application->id, false);
})->with([
    'administrasi' => ['administrasi', RecruitmentStage::APPLIED],
    'on progress' => ['on-progress', RecruitmentStage::ADMINISTRASI],
    'riwayat' => ['riwayat', RecruitmentStage::REJECTED],
]);

test('job applicants page renders a reachable detail link', function (): void {
    [$admin, , $job, $application] = ($this->makeCandidateReviewFixture)();

    Livewire::actingAs($admin)
        ->test(ApplicationManagement::class, ['job' => $job])
        ->assertSee('/applications/'.$job->id.'/'.$application->id, false);
});
