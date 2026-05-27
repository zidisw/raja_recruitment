<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\Candidate\JobPortal;
use App\Livewire\Candidate\ProfileSetup;
use App\Livewire\CandidateManagement;
use App\Livewire\InterviewManagement;
use App\Livewire\McuManagement;
use App\Livewire\OfferingLetterManagement;
use App\Livewire\OnboardingManagement;
use App\Livewire\PsychotestManagement;
use App\Livewire\PtkManagement;
use App\Models\Application;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Mcu;
use App\Models\OfferingLetter;
use App\Models\Onboarding;
use App\Models\Psychotest;
use App\Models\Ptk;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('candidate can move end to end from profile setup to hired', function () {
    Storage::fake('public');

    $department = Department::factory()->create(['name' => 'Operations E2E']);
    $site = Site::factory()->create(['name' => 'Site E2E Morowali']);
    $admin = User::factory()->create([
        'name' => 'E2E HR Admin',
        'role' => UserRole::Admin,
        'department_id' => $department->id,
    ]);
    $interviewer = User::factory()->create([
        'name' => 'E2E Interviewer',
        'role' => UserRole::Admin,
        'department_id' => $department->id,
    ]);
    $candidate = User::factory()->create([
        'name' => 'E2E Candidate',
        'email' => 'candidate.e2e@example.com',
        'role' => UserRole::User,
    ]);

    Livewire::actingAs($admin)
        ->test(PtkManagement::class)
        ->set('mode', 'create')
        ->set('nomor_ptk', 'E2E-PTK-001')
        ->set('department', $department->name)
        ->set('posisi', 'E2E Heavy Equipment Operator')
        ->set('jumlah_kebutuhan', 1)
        ->set('alasan_permintaan', 'End-to-end recruitment test')
        ->set('tanggal_permintaan', now()->toDateString())
        ->set('status', 'approved')
        ->call('save')
        ->assertHasNoErrors();

    $ptk = Ptk::where('nomor_ptk', 'E2E-PTK-001')->firstOrFail();

    $job = Job::create([
        'title' => $ptk->posisi,
        'description' => 'End-to-end test job description',
        'requirements' => 'End-to-end test requirements',
        'benefits' => 'End-to-end test benefits',
        'level' => 'staff',
        'is_active' => true,
        'closed_at' => now()->addMonth()->toDateString(),
        'created_by' => $admin->id,
        'department_id' => $department->id,
        'site_id' => $site->id,
        'ptk_id' => $ptk->id,
    ]);

    Livewire::actingAs($candidate)
        ->test(ProfileSetup::class)
        ->set('nik', '7300000000000001')
        ->set('place_of_birth', 'Makassar')
        ->set('date_of_birth', now()->subYears(27)->toDateString())
        ->set('gender', 'male')
        ->set('religion', 'Islam')
        ->set('marital_status', 'single')
        ->set('address_ktp', 'Jl. KTP E2E No. 1')
        ->set('address_domicile', 'Jl. Domisili E2E No. 1')
        ->set('whatsapp', '081234567890')
        ->set('linkedin_url', 'https://linkedin.com/in/e2e-candidate')
        ->set('photo', UploadedFile::fake()->image('candidate.jpg'))
        ->set('ktp_file', UploadedFile::fake()->create('ktp.pdf', 80, 'application/pdf'))
        ->set('educations', [[
            'degree' => 'D3',
            'institution_name' => 'Politeknik E2E',
            'major' => 'Teknik Mesin',
            'start_year' => 2017,
            'end_year' => 2020,
            'gpa' => 3.42,
        ]])
        ->set('experiences', [[
            'company_name' => 'PT E2E Mining',
            'position' => 'Operator',
            'start_date' => now()->subYears(3)->toDateString(),
            'end_date' => now()->subMonth()->toDateString(),
            'is_current' => false,
            'last_salary' => 6000000,
            'job_description' => 'Operating heavy equipment',
        ]])
        ->set('organizations', [[
            'organization_name' => 'Komunitas E2E',
            'position' => 'Member',
            'start_date' => now()->subYears(2)->toDateString(),
            'end_date' => null,
            'is_current' => true,
        ]])
        ->set('portfolio', UploadedFile::fake()->create('portfolio.pdf', 80, 'application/pdf'))
        ->set('certificate', UploadedFile::fake()->create('certificate.pdf', 80, 'application/pdf'))
        ->set('paklaring', UploadedFile::fake()->create('paklaring.pdf', 80, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('candidate.portal'));

    Livewire::actingAs($candidate)
        ->test(JobPortal::class)
        ->call('apply', $job)
        ->assertSet('showConfirmModal', true)
        ->call('confirmApply')
        ->assertHasNoErrors()
        ->assertSet('showConfirmModal', false);

    $application = Application::whereBelongsTo($candidate, 'candidate')
        ->whereBelongsTo($job)
        ->firstOrFail();

    expect($application->recruitment_stage)->toBe(RecruitmentStage::APPLIED);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'administrasi'])
        ->call('passAdministrative', $application->id)
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::HR_INTERVIEW);

    Livewire::actingAs($admin)
        ->test(CandidateManagement::class, ['tab' => 'on-progress'])
        ->call('openScheduleInterview', $application->id)
        ->set('interviewer_id', $interviewer->id)
        ->set('scheduled_date', now()->addDay()->toDateString())
        ->set('scheduled_time', '09:00')
        ->set('hr_notes', 'HR interview E2E schedule')
        ->call('saveInterview')
        ->assertHasNoErrors();

    $hrInterview = Interview::where('application_id', $application->id)
        ->where('interview_type', 'HR Interview')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(InterviewManagement::class, ['tab' => 'hr'])
        ->call('openUploadModal', $hrInterview->id)
        ->set('upload_file', UploadedFile::fake()->create('hr-evaluation.pdf', 80, 'application/pdf'))
        ->call('saveUpload')
        ->call('updateInterviewStatus', $hrInterview->id, 'passed')
        ->assertHasNoErrors();

    Livewire::actingAs($admin)
        ->test(InterviewManagement::class, ['tab' => 'user'])
        ->call('openScheduleUserInterview', $application->id)
        ->set('interviewer_id', $interviewer->id)
        ->set('scheduled_date', now()->addDays(2)->toDateString())
        ->set('scheduled_time', '13:30')
        ->set('status', 'scheduled')
        ->set('hr_notes', 'User interview E2E schedule')
        ->call('save')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::USER_INTERVIEW);

    $userInterview = Interview::where('application_id', $application->id)
        ->where('interview_type', 'User Interview')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(InterviewManagement::class, ['tab' => 'user'])
        ->call('openUploadModal', $userInterview->id)
        ->set('upload_file', UploadedFile::fake()->create('user-evaluation.pdf', 80, 'application/pdf'))
        ->call('saveUpload')
        ->call('updateInterviewStatus', $userInterview->id, 'passed')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::OFFERING);

    Livewire::actingAs($admin)
        ->test(OfferingLetterManagement::class)
        ->call('openCreate', $application->id)
        ->set('offer_date', now()->toDateString())
        ->set('status', 'accepted')
        ->set('offer_file', UploadedFile::fake()->create('offering.pdf', 80, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::PSYCHOTEST);
    expect(OfferingLetter::where('application_id', $application->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(PsychotestManagement::class)
        ->call('openCreate', $application->id)
        ->set('test_date', now()->toDateString())
        ->set('result', 'passed')
        ->set('notes', 'Psychotest passed in E2E test')
        ->set('psychotest_file', UploadedFile::fake()->create('psychotest.pdf', 80, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::MCU);
    expect(Psychotest::where('application_id', $application->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(McuManagement::class)
        ->call('openCreate', $application->id)
        ->set('mcu_date', now()->toDateString())
        ->set('result', 'fit')
        ->set('notes', 'MCU fit in E2E test')
        ->set('mcu_file', UploadedFile::fake()->create('mcu.pdf', 80, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::ONBOARDING);
    expect(Mcu::where('application_id', $application->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(OnboardingManagement::class)
        ->call('openCreate', $application->id)
        ->set('joining_date', now()->addWeek()->toDateString())
        ->set('onboarding_status', 'completed')
        ->set('travel_ticket_number', 'TKT-E2E-001')
        ->set('travel_ticket_notes', 'Ticket issued in E2E test')
        ->set('onsite_date', now()->addDays(6)->toDateString())
        ->set('onsite_location', 'Site E2E Morowali')
        ->set('onsite_notes', 'Onsite onboarding completed')
        ->call('save')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->recruitment_stage)->toBe(RecruitmentStage::HIRED);
    expect(Onboarding::where('application_id', $application->id)->where('onboarding_status', 'completed')->exists())->toBeTrue();
    expect($application->stageLogs()->count())->toBeGreaterThanOrEqual(7);
});
