<?php

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Livewire\EmailTemplateManagement;
use App\Models\EmailTemplate;
use App\Models\User;
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
        ->set('subject', 'Application Update')
        ->set('body', 'Dear {name}, your application for {job} is now {status}.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $template = EmailTemplate::where('stage', RecruitmentStage::ADMINISTRASI->value)
        ->where('job_level', 'staff')
        ->first();

    expect($template)->not->toBeNull()
        ->and($template->subject)->toBe('Application Update')
        ->and($template->getRawOriginal('stage'))->toBe(RecruitmentStage::ADMINISTRASI->value);
});
