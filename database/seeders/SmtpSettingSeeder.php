<?php

namespace Database\Seeders;

use App\Models\SmtpSetting;
use Illuminate\Database\Seeder;

class SmtpSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (SmtpSetting::count() === 0) {
            SmtpSetting::create([
                'host' => 'mail.rodajayasakti.id',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => 'noreply@rodajayasakti.id',
                'password' => 'Noreply12#',
                'from_address' => 'noreply@rodajayasakti.id',
                'from_name' => 'PT. Roda Jaya Sakti',
            ]);
        }
    }
}
