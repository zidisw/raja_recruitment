<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasIndex('jobs', 'jobs_department_site_active_index')) {
                $table->index(['department_id', 'site_id', 'is_active'], 'jobs_department_site_active_index');
            }

            if (! Schema::hasIndex('jobs', 'jobs_updated_at_index')) {
                $table->index('updated_at', 'jobs_updated_at_index');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasIndex('applications', 'applications_stage_updated_at_index')) {
                $table->index(['recruitment_stage', 'updated_at'], 'applications_stage_updated_at_index');
            }
        });

        Schema::table('interviews', function (Blueprint $table) {
            if (! Schema::hasIndex('interviews', 'interviews_type_scheduled_at_index')) {
                $table->index(['interview_type', 'scheduled_at'], 'interviews_type_scheduled_at_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasIndex('jobs', 'jobs_department_site_active_index')) {
                $table->dropIndex('jobs_department_site_active_index');
            }

            if (Schema::hasIndex('jobs', 'jobs_updated_at_index')) {
                $table->dropIndex('jobs_updated_at_index');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasIndex('applications', 'applications_stage_updated_at_index')) {
                $table->dropIndex('applications_stage_updated_at_index');
            }
        });

        Schema::table('interviews', function (Blueprint $table) {
            if (Schema::hasIndex('interviews', 'interviews_type_scheduled_at_index')) {
                $table->dropIndex('interviews_type_scheduled_at_index');
            }
        });
    }
};
