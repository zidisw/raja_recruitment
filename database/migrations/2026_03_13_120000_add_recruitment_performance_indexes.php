<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->index('status');
            $table->index('stage_updated_at');
            $table->index('created_at');
            $table->index(['job_id', 'recruitment_stage'], 'applications_job_stage_index');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at'], 'interviews_status_scheduled_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['stage_updated_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex('applications_job_stage_index');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->dropIndex('interviews_status_scheduled_at_index');
        });
    }
};
