<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

class DepartmentFixSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Operations' => 'Mining Operation',
            'Human Resources' => 'HRGA',
            'Health, Safety & Environment' => 'HSE',
            'Engineering' => 'Mining Engineering',
            'Procurement & Logistics' => 'Logistic',
        ];

        $allowedNames = ['HRGA', 'HSE', 'Mining Engineering', 'Mining Operation', 'Logistic', 'PLM'];
        $targetIds = [];

        foreach ($allowedNames as $name) {
            $targetIds[$name] = Department::firstOrCreate(['name' => $name])->id;
        }

        foreach (Department::all() as $dept) {
            if (in_array($dept->name, $allowedNames)) {
                continue;
            }

            if (isset($map[$dept->name])) {
                $targetName = $map[$dept->name];
                $targetId = $targetIds[$targetName];
                
                Job::where('department_id', $dept->id)->update(['department_id' => $targetId]);
                
                $dept->delete();
            } else {
                if (Job::where('department_id', $dept->id)->exists()) {
                     Job::where('department_id', $dept->id)->update(['department_id' => $targetIds['HRGA']]);
                }
                $dept->delete();
            }
        }
        
        // Fix string-based PTK departments
        foreach ($map as $old => $new) {
            DB::table('ptk')->where('department', $old)->update(['department' => $new]);
        }
        DB::table('ptk')
            ->whereNotIn('department', $allowedNames)
            ->update(['department' => 'HRGA']);

        // 3. Move ADMIN_REVIEW to HR_INTERVIEW
        try {
            DB::table('applications')->where('recruitment_stage', 'ADMIN_REVIEW')->update(['recruitment_stage' => 'HR_INTERVIEW']);
        } catch (\Exception $e) {}
        
        try {
            DB::statement("UPDATE application_stage_logs SET stage = 'HR_INTERVIEW' WHERE stage = 'ADMIN_REVIEW'");
        } catch (\Exception $e) {}
    }
}
