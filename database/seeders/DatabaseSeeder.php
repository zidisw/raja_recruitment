<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            SiteSeeder::class,
            SmtpSettingSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Rivaldo Pieter',
            'email' => 'rivaldopieterlinogi@gmail.com',
            'role' => UserRole::SuperAdmin,
        ]);

        User::factory()->create([
            'name' => 'HR Manager',
            'email' => 'hr@example.com',
            'role' => UserRole::Admin,
            'department_id' => 2, // Human Resources
        ]);

        User::factory()->create([
            'name' => 'Recruitment Admin',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin,
            'department_id' => 2, // Human Resources
        ]);

        for ($i = 1; $i <= 4; $i++) {
            User::factory()->create([
                'name' => 'User '.$i,
                'email' => 'candidate'.$i.'@example.com',
                'role' => UserRole::User,
            ]);
        }
    }
}
