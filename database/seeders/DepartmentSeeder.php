<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Operations', 'description' => 'Manages day-to-day mining and construction operations.'],
            ['name' => 'Human Resources', 'description' => 'Handles recruitment, employee relations, and HR administration.'],
            ['name' => 'Finance & Accounting', 'description' => 'Manages financial reporting, budgeting, and accounting.'],
            ['name' => 'Engineering', 'description' => 'Responsible for technical planning, design, and project execution.'],
            ['name' => 'Health, Safety & Environment', 'description' => 'Ensures compliance with HSE standards and policies.'],
            ['name' => 'Heavy Equipment', 'description' => 'Manages heavy equipment fleet, maintenance, and rental services.'],
            ['name' => 'Information Technology', 'description' => 'Manages IT infrastructure, systems, and support.'],
            ['name' => 'Procurement & Logistics', 'description' => 'Handles procurement, supply chain, and logistics.'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department['name']], $department);
        }
    }
}
