<?php

declare(strict_types=1);

namespace App\Livewire\Superadmin;

use App\Enums\UserRole;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SmtpSettings extends Component
{
    public string $host = '';

    public int $port = 465;

    public string $encryption = 'ssl';

    public string $username = '';

    public string $password = '';

    public string $from_address = '';

    public string $from_name = '';

    public string $testStatus = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->role === UserRole::SuperAdmin, 403);

        $smtp = SmtpSetting::first();

        if ($smtp) {
            $this->host = $smtp->host;
            $this->port = $smtp->port;
            $this->encryption = $smtp->encryption;
            $this->username = $smtp->username;
            $this->from_address = $smtp->from_address;
            $this->from_name = $smtp->from_name;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'host' => ['required', 'string'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'encryption' => ['required', 'string', 'in:ssl,tls,starttls,'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
            'from_address' => ['required', 'email'],
            'from_name' => ['required', 'string'],
        ]);

        // If no new password provided, preserve the existing one from DB
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $smtp = SmtpSetting::updateOrCreate(['id' => 1], $validated);

        // Reload config immediately so outgoing mail uses new settings
        Config::set('mail.mailers.smtp.host', $validated['host']);
        Config::set('mail.mailers.smtp.port', $validated['port']);
        Config::set('mail.mailers.smtp.encryption', $validated['encryption']);
        Config::set('mail.mailers.smtp.username', $validated['username']);
        Config::set('mail.mailers.smtp.password', $smtp->password); // use decrypted from model
        Config::set('mail.from.address', $validated['from_address']);
        Config::set('mail.from.name', $validated['from_name']);

        $this->dispatch('notify', ['message' => 'SMTP settings saved successfully.', 'type' => 'success']);
    }

    public function sendTestEmail(): void
    {
        $this->testStatus = '';

        try {
            $recipientEmail = auth()->user()->email;
            $recipientName = auth()->user()->name;

            Mail::raw(
                'This is a test email from PT. Roda Jaya Sakti Recruitment System. SMTP configuration is working correctly.',
                function ($message) use ($recipientEmail, $recipientName) {
                    $message->to($recipientEmail, $recipientName)
                        ->subject('Test Email — RJS Recruitment System');
                }
            );

            $this->testStatus = 'success';
        } catch (\Exception $e) {
            $this->testStatus = 'error:'.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.superadmin.smtp-settings');
    }
}
