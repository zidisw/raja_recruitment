<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     $this->configureDefaults();
    // }

    public function boot(): void
    {
        $this->loadSmtpFromDatabase();

        VerifyEmail::createUrlUsing(function (object $notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }

    protected function loadSmtpFromDatabase(): void
    {
        try {
            $smtp = SmtpSetting::first();

            if ($smtp) {
                Config::set('mail.mailers.smtp.host', $smtp->host);
                Config::set('mail.mailers.smtp.port', $smtp->port);
                Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
                Config::set('mail.mailers.smtp.username', $smtp->username);
                Config::set('mail.mailers.smtp.password', $smtp->password);
                Config::set('mail.from.address', $smtp->from_address);
                Config::set('mail.from.name', $smtp->from_name);
            }
        } catch (\Exception) {
            // Database may not be available during migrations/setup
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
