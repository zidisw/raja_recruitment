<?php

use App\Livewire\Frontend\Contact;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('contact page loads successfully', function () {
    $this->get(route('contact'))->assertOk();
});

test('contact page renders the contact livewire component', function () {
    $this->get(route('contact'))->assertSeeLivewire(Contact::class);
});

test('contact form validates required fields', function () {
    Livewire::test(Contact::class)
        ->call('sendMessage')
        ->assertHasErrors(['name', 'email', 'subject', 'message']);
});

test('contact form validates email format', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Test User')
        ->set('email', 'not-an-email')
        ->set('subject', 'Test Subject')
        ->set('message', 'This is a test message body.')
        ->call('sendMessage')
        ->assertHasErrors(['email']);
});

test('contact form validates message minimum length', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('subject', 'Test Subject')
        ->set('message', 'Short')
        ->call('sendMessage')
        ->assertHasErrors(['message']);
});

test('contact form shows error when smtp is not configured', function () {
    SmtpSetting::query()->delete();

    Livewire::test(Contact::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('subject', 'Test Subject')
        ->set('message', 'This is a longer test message for the contact form.')
        ->call('sendMessage')
        ->assertSet('submitted', false)
        ->assertSet('errorMessage', 'Email service is not configured yet. Please contact us directly via phone.');
});

test('contact form sends email and shows success state when smtp is configured', function () {
    Mail::fake();

    SmtpSetting::create([
        'host' => 'mail.rodajayasakti.id',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'noreply@rodajayasakti.id',
        'password' => 'Noreply12#',
        'from_address' => 'noreply@rodajayasakti.id',
        'from_name' => 'PT. Roda Jaya Sakti',
    ]);

    Livewire::test(Contact::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('phone', '081234567890')
        ->set('subject', 'Kerjasama Bisnis')
        ->set('message', 'Kami tertarik untuk menjalin kerjasama dengan PT. Roda Jaya Sakti.')
        ->call('sendMessage')
        ->assertSet('submitted', true)
        ->assertSet('errorMessage', '');
});

test('contact page nav has contact link', function () {
    $this->get(route('contact'))
        ->assertSee(route('contact'));
});
