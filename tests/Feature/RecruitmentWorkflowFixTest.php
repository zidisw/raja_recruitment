<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\Candidate\MyApplications;
use App\Livewire\CandidateManagement;
use App\Livewire\OfferingLetterManagement;
use App\Models\Application;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Job;
use App\Models\OfferingLetter;
use App\Models\Site;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\RecruitmentEventNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function createRecruitmentWorkflowFixture(RecruitmentStage $stage = RecruitmentStage::APPLIED): array
{
    $department = Department::factory()->create(['name' => 'Workflow Department']);
    $site = Site::factory()->create(['name' => 'Workflow Site']);
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'department_id' => $department->id,
    ]);
    $interviewer = User::factory()->create([
        'role' => UserRole::Admin,
        'department_id' => $department->id,
    ]);
    $candidate = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $job = Job::create([
        'title' => 'Workflow Operator',
        'description' => 'Workflow description',
        'requirements' => 'Workflow requirements',
        'level' => 'staff',
        'is_active' => true,
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

    return [$admin, $interviewer, $candidate, $job, $application];
}

test('administrative pass enters on progress and HR interview scheduling advances stage without duplicates', function (): void {
    Notification::fake();

    [$admin, $interviewer, $candidate, $job, $application] = createRecruitmentWorkflowFixture();

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'administrasi'])
        ->call('passAdministrative', $application->id)
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::ADMINISTRASI);

    Notification::assertSentTo($candidate, ApplicationStatusUpdatedNotification::class);
    Notification::assertSentTo($candidate, ApplicationStatusChanged::class);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'on-progress'])
        ->call('openScheduleInterview', $application->id)
        ->set('interviewer_id', $interviewer->id)
        ->set('scheduled_date', now()->addDay()->toDateString())
        ->set('scheduled_time', '09:00')
        ->call('saveInterview')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::HR_INTERVIEW)
        ->and(Interview::where('application_id', $application->id)->where('interview_type', 'HR Interview')->count())->toBe(1);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'on-progress'])
        ->assertDontSee('/applications/'.$job->id.'/'.$application->id, false);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'on-progress'])
        ->call('openScheduleInterview', $application->id)
        ->set('interviewer_id', $interviewer->id)
        ->set('scheduled_date', now()->addDays(2)->toDateString())
        ->set('scheduled_time', '10:30')
        ->call('saveInterview')
        ->assertHasNoErrors();

    expect(Interview::where('application_id', $application->id)->where('interview_type', 'HR Interview')->count())->toBe(1);

    Notification::assertSentTo($candidate, RecruitmentEventNotification::class);
    Notification::assertSentTo($interviewer, RecruitmentEventNotification::class);
});

test('offering letter waits for signed upload before admin validation advances candidate', function (): void {
    Storage::fake('public');
    Notification::fake();

    [$admin, , $candidate, , $application] = createRecruitmentWorkflowFixture(RecruitmentStage::OFFERING);

    Livewire::actingAs($admin)
        ->test(OfferingLetterManagement::class)
        ->call('openCreate', $application->id)
        ->set('offer_date', now()->toDateString())
        ->set('status', 'waiting_response')
        ->set('offer_file', UploadedFile::fake()->create('offering.pdf', 80, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $offering = OfferingLetter::where('application_id', $application->id)->firstOrFail();
    expect($offering->file_path)->not->toBeNull()
        ->and($offering->signed_file_path)->toBeNull()
        ->and($application->refresh()->recruitment_stage)->toBe(RecruitmentStage::OFFERING);

    Livewire::actingAs($admin)
        ->test(OfferingLetterManagement::class)
        ->call('openEdit', $offering->id)
        ->set('status', 'accepted')
        ->call('save')
        ->assertHasNoErrors();

    expect($application->refresh()->recruitment_stage)->toBe(RecruitmentStage::OFFERING)
        ->and($offering->refresh()->status)->toBe('waiting_response');

    Livewire::actingAs($candidate)
        ->test(MyApplications::class)
        ->set('signed_ol_file', UploadedFile::fake()->create('offering-signed.pdf', 80, 'application/pdf'))
        ->call('uploadSignedOL', $application->id)
        ->assertHasNoErrors();

    $offering->refresh();
    expect($offering->signed_file_path)->not->toBeNull()
        ->and($offering->status)->toBe('signed');

    Livewire::actingAs($admin)
        ->test(OfferingLetterManagement::class)
        ->call('openEdit', $offering->id)
        ->set('status', 'accepted')
        ->call('save')
        ->assertHasNoErrors();

    expect($application->refresh()->recruitment_stage)->toBe(RecruitmentStage::PSYCHOTEST)
        ->and($offering->refresh()->status)->toBe('accepted');

    Notification::assertSentTo($candidate, RecruitmentEventNotification::class);
    Notification::assertSentTo($candidate, ApplicationStatusUpdatedNotification::class);
    Notification::assertSentTo($admin, RecruitmentEventNotification::class);
});
