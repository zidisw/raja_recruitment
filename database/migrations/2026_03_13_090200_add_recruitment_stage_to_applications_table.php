<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'recruitment_stage')) {
                $table->string('recruitment_stage')->default('APPLIED')->after('status');
                $table->index('recruitment_stage');
            }
        });

        DB::statement("UPDATE applications
            SET recruitment_stage = CASE status
                WHEN 0 THEN 'APPLIED'
                WHEN 1 THEN 'ADMIN_REVIEW'
                WHEN 2 THEN 'HR_INTERVIEW'
                WHEN 3 THEN 'USER_INTERVIEW'
                WHEN 4 THEN 'PSYCHOTEST'
                WHEN 5 THEN 'OFFERING'
                WHEN 6 THEN 'MCU'
                WHEN 7 THEN 'HIRED'
                WHEN 99 THEN 'REJECTED'
                ELSE 'APPLIED'
            END");
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'recruitment_stage')) {
                $table->dropIndex(['recruitment_stage']);
                $table->dropColumn('recruitment_stage');
            }
        });
    }
};
