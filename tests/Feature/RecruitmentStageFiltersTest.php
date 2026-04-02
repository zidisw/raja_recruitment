<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\InterviewManagement;
use App\Livewire\McuManagement;
use App\Livewire\OfferingLetterManagement;
use App\Livewire\OnboardingManagement;
use App\Livewire\PsychotestManagement;
use App\Models\Application;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Mcu;
use App\Models\OfferingLetter;
use App\Models\Onboarding;
use App\Models\Ptk;
use App\Models\Psychotest;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

function makePtk(User $creator, string $position): Ptk
{
    return Ptk::create([
        'nomor_ptk' => 'PTK-' . uniqid(),
        'department' => 'HR',
        'posisi' => $position,
        'jumlah_kebutuhan' => 1,
        'alasan_permintaan' => 'Recruitment test fixture',
        'tanggal_permintaan' => now()->toDateString(),
        'status' => 'approved',
        'created_by' => $creator->id,
    ]);
}

function makeJob(User $creator, Department $department, Site $site, string $title): Job
{
    $ptk = makePtk($creator, $title);

    return Job::create([
        'title' => $title,
        'description' => 'Test description',
        'requirements' => 'Test requirements',
        'benefits' => null,
        'level' => 'staff',
        'is_active' => true,
        'created_by' => $creator->id,
        'department_id' => $department->id,
        'site_id' => $site->id,
        'ptk_id' => $ptk->id,
    ]);
}

function makeApplication(User $candidate, Job $job, RecruitmentStage $stage): Application
{
    return Application::create([
        'user_id' => $candidate->id,
        'job_id' => $job->id,
        'recruitment_stage' => $stage,
    ]);
}

test('interview management filters by department and status', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $interviewer = User::factory()->create(['role' => UserRole::HR]);

    $deptA = Department::factory()->create(['name' => 'Dept A']);
    $deptB = Department::factory()->create(['name' => 'Dept B']);
    $site = Site::factory()->create();

    $candidateA = User::factory()->create(['name' => 'Alice Filter']);
    $candidateB = User::factory()->create(['name' => 'Bob Hidden']);

    $jobA = makeJob($admin, $deptA, $site, 'Operator A');
    $jobB = makeJob($admin, $deptB, $site, 'Operator B');

    $appA = makeApplication($candidateA, $jobA, RecruitmentStage::HR_INTERVIEW);
    $appB = makeApplication($candidateB, $jobB, RecruitmentStage::HR_INTERVIEW);

    Interview::create([
        'application_id' => $appA->id,
        'interview_type' => 'HR Interview',
        'interviewer_id' => $interviewer->id,
        'scheduled_at' => now()->addDay(),
        'status' => 'scheduled',
    ]);

    Interview::create([
        'application_id' => $appB->id,
        'interview_type' => 'HR Interview',
        'interviewer_id' => $interviewer->id,
        'scheduled_at' => now()->addDays(2),
        'status' => 'passed',
        'evaluation_path' => 'interviews/evaluations/bob.pdf',
    ]);

    Livewire::actingAs($admin)
        ->test(InterviewManagement::class, ['tab' => 'hr'])
        ->set('filterDepartment', (string) $deptA->id)
        ->set('filterStatus', 'scheduled')
        ->assertViewHas('interviews', function ($paginator) use ($appA): bool {
            $ids = $paginator->getCollection()->pluck('application_id')->all();

            return count($ids) === 1 && (int) $ids[0] === $appA->id;
        });
});

test('offering management filters by offer status', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create();
    $site = Site::factory()->create();

    $candidateA = User::factory()->create(['name' => 'Offer Accepted']);
    $candidateB = User::factory()->create(['name' => 'Offer Rejected']);

    $jobA = makeJob($admin, $department, $site, 'Offer Job A');
    $jobB = makeJob($admin, $department, $site, 'Offer Job B');

    $appA = makeApplication($candidateA, $jobA, RecruitmentStage::OFFERING);
    $appB = makeApplication($candidateB, $jobB, RecruitmentStage::OFFERING);

    OfferingLetter::create([
        'application_id' => $appA->id,
        'offer_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    OfferingLetter::create([
        'application_id' => $appB->id,
        'offer_date' => now()->toDateString(),
        'status' => 'rejected',
    ]);

    Livewire::actingAs($admin)
        ->test(OfferingLetterManagement::class)
        ->set('filterStatus', 'accepted')
        ->assertViewHas('applications_paginated', function ($paginator) use ($appA): bool {
            $ids = $paginator->getCollection()->pluck('id')->all();

            return count($ids) === 1 && (int) $ids[0] === $appA->id;
        });
});

test('psychotest management filters by result', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create();
    $site = Site::factory()->create();

    $candidateA = User::factory()->create(['name' => 'Psych Passed']);
    $candidateB = User::factory()->create(['name' => 'Psych Failed']);

    $jobA = makeJob($admin, $department, $site, 'Psych Job A');
    $jobB = makeJob($admin, $department, $site, 'Psych Job B');

    $appA = makeApplication($candidateA, $jobA, RecruitmentStage::PSYCHOTEST);
    $appB = makeApplication($candidateB, $jobB, RecruitmentStage::PSYCHOTEST);

    Psychotest::create([
        'application_id' => $appA->id,
        'test_date' => now()->toDateString(),
        'result' => 'passed',
    ]);

    Psychotest::create([
        'application_id' => $appB->id,
        'test_date' => now()->toDateString(),
        'result' => 'failed',
    ]);

    Livewire::actingAs($admin)
        ->test(PsychotestManagement::class)
        ->set('filterResult', 'passed')
        ->assertViewHas('applications_paginated', function ($paginator) use ($appA): bool {
            $ids = $paginator->getCollection()->pluck('id')->all();

            return count($ids) === 1 && (int) $ids[0] === $appA->id;
        });
});

test('mcu management filters by result', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create();
    $site = Site::factory()->create();

    $candidateA = User::factory()->create(['name' => 'MCU Fit']);
    $candidateB = User::factory()->create(['name' => 'MCU Unfit']);

    $jobA = makeJob($admin, $department, $site, 'MCU Job A');
    $jobB = makeJob($admin, $department, $site, 'MCU Job B');

    $appA = makeApplication($candidateA, $jobA, RecruitmentStage::MCU);
    $appB = makeApplication($candidateB, $jobB, RecruitmentStage::MCU);

    Mcu::create([
        'application_id' => $appA->id,
        'mcu_date' => now()->toDateString(),
        'result' => 'fit',
    ]);

    Mcu::create([
        'application_id' => $appB->id,
        'mcu_date' => now()->toDateString(),
        'result' => 'unfit',
    ]);

    Livewire::actingAs($admin)
        ->test(McuManagement::class)
        ->set('filterResult', 'fit')
        ->assertViewHas('applications_paginated', function ($paginator) use ($appA): bool {
            $ids = $paginator->getCollection()->pluck('id')->all();

            return count($ids) === 1 && (int) $ids[0] === $appA->id;
        });
});

test('onboarding management filters by onboarding status', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create();
    $site = Site::factory()->create();

    $candidateA = User::factory()->create(['name' => 'Onboard Pending']);
    $candidateB = User::factory()->create(['name' => 'Onboard Completed']);

    $jobA = makeJob($admin, $department, $site, 'Onboarding Job A');
    $jobB = makeJob($admin, $department, $site, 'Onboarding Job B');

    $appA = makeApplication($candidateA, $jobA, RecruitmentStage::ONBOARDING);
    $appB = makeApplication($candidateB, $jobB, RecruitmentStage::HIRED);

    Onboarding::create([
        'application_id' => $appA->id,
        'joining_date' => now()->toDateString(),
        'onboarding_status' => 'pending',
    ]);

    Onboarding::create([
        'application_id' => $appB->id,
        'joining_date' => now()->toDateString(),
        'onboarding_status' => 'completed',
    ]);

    Livewire::actingAs($admin)
        ->test(OnboardingManagement::class)
        ->set('filterStatus', 'completed')
        ->assertViewHas('applicationsPaginated', function ($paginator) use ($appB): bool {
            $ids = $paginator->getCollection()->pluck('id')->all();

            return count($ids) === 1 && (int) $ids[0] === $appB->id;
        });
});
