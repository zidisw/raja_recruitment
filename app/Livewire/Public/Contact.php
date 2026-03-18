<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Contact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public bool $submitted = false;

    public string $errorMessage = '';

    public function sendMessage(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $smtp = SmtpSetting::first();

        if (! $smtp) {
            $this->errorMessage = 'Email service is not configured yet. Please contact us directly via phone.';

            return;
        }

        Config::set('mail.mailers.smtp.host', $smtp->host);
        Config::set('mail.mailers.smtp.port', $smtp->port);
        Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
        Config::set('mail.mailers.smtp.username', $smtp->username);
        Config::set('mail.mailers.smtp.password', $smtp->password);
        Config::set('mail.from.address', $smtp->from_address);
        Config::set('mail.from.name', $smtp->from_name);

        try {
            $senderName = $this->name;
            $senderEmail = $this->email;
            $senderPhone = $this->phone;
            $msgSubject = $this->subject;
            $msgBody = $this->message;

            Mail::html(
                view('emails.contact-form', [
                    'senderName' => $senderName,
                    'senderEmail' => $senderEmail,
                    'senderPhone' => $senderPhone,
                    'msgBody' => $msgBody,
                ])->render(),
                function ($mail) use ($senderName, $senderEmail, $msgSubject) {
                    $mail->to('info@rodajayasakti.id', 'PT. Roda Jaya Sakti')
                        ->replyTo($senderEmail, $senderName)
                        ->subject('[Contact Form] '.$msgSubject);
                }
            );

            $this->submitted = true;
            $this->errorMessage = '';
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to send your message. Please try again or contact us directly.';
        }
    }

    public function render(): View
    {
        return view('livewire.public.contact');
    }
}
