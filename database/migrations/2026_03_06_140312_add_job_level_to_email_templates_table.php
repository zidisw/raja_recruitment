<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('job_level')->default('staff')->after('stage'); // 'staff' or 'non_staff'
            $table->dropUnique('email_templates_stage_unique');
        });

        // Duplicate existing records for non_staff
        $existing = DB::table('email_templates')->get();
        foreach ($existing as $template) {
            DB::table('email_templates')->insert([
                'stage' => $template->stage,
                'job_level' => 'non_staff',
                'subject' => $template->subject,
                'body' => $template->body,
                'created_at' => $template->created_at,
                'updated_at' => $template->updated_at,
            ]);
        }

        Schema::table('email_templates', function (Blueprint $table) {
            $table->unique(['stage', 'job_level']);
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropUnique(['stage', 'job_level']);
        });

        DB::table('email_templates')->where('job_level', 'non_staff')->delete();

        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('job_level');
            $table->unique('stage');
        });
    }
};
