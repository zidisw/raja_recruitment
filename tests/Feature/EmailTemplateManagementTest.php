<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\EmailTemplateManagement;
use App\Models\Application;
use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('email template page renders quoted stage action parameters', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    actingAs($user);

    $this->get(route('email-templates.index'))
        ->assertOk()
        ->assertSee("openEdit('ADMINISTRASI', 'staff')", false)
        ->assertSee("openEdit('ADMINISTRASI', 'non_staff')", false);
});

test('superadmin can create an email template', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    Livewire::actingAs($user)
        ->test(EmailTemplateManagement::class)
        ->call('openEdit', RecruitmentStage::ADMINISTRASI->value, 'staff')
        ->set('subject', 'Hasil Seleksi Administrasi - {job}')
        ->set('body', 'Yth. {name}, lamaran Anda untuk {job} sudah masuk tahap {status}.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $template = EmailTemplate::where('stage', RecruitmentStage::ADMINISTRASI->value)
        ->where('job_level', 'staff')
        ->first();

    expect($template)->not->toBeNull()
        ->and($template->subject)->toBe('Hasil Seleksi Administrasi - {job}')
        ->and($template->getRawOriginal('stage'))->toBe(RecruitmentStage::ADMINISTRASI->value);
});

test('email template form uses Indonesian defaults', function () {
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);

    $component = Livewire::actingAs($user)
        ->test(EmailTemplateManagement::class)
        ->call('openEdit', RecruitmentStage::OFFERING->value, 'staff');

    expect($component->get('subject'))->toBe('Offering Letter - {job}');
    expect($component->get('body'))
        ->toContain('Yth. {name},')
        ->toContain('Offering Letter untuk posisi {job}')
        ->toContain('Tim Rekrutmen PT Roda Jaya Sakti');
});

test('status email replaces placeholders in subject and body', function () {
    $candidate = User::factory()->make(['name' => 'Budi Santoso']);
    $job = new Job(['title' => 'Operator Produksi', 'level' => 'staff']);
    $application = new Application(['recruitment_stage' => RecruitmentStage::HR_INTERVIEW]);
    $application->setRelation('candidate', $candidate);
    $application->setRelation('job', $job);

    EmailTemplate::updateOrCreate(
        ['stage' => RecruitmentStage::HR_INTERVIEW->value, 'job_level' => 'staff'],
        [
            'subject' => 'Tahap {status} - {job}',
            'body' => 'Yth. {name}, status lamaran Anda untuk {job} adalah {stage}.',
        ]
    );

    $mail = (new ApplicationStatusChanged($application))->toMail($candidate);

    expect($mail->subject)->toBe('Tahap Interview HR - Operator Produksi');
    expect($mail->introLines[0])->toBe('Yth. Budi Santoso, status lamaran Anda untuk Operator Produksi adalah Interview HR.');
});
