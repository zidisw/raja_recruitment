<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            if (! Schema::hasColumn('interviews', 'interview_type')) {
                $table->string('interview_type')->default('HR Interview')->after('application_id');
            }

            if (! Schema::hasColumn('interviews', 'evaluation_path')) {
                $table->string('evaluation_path')->nullable()->after('meeting_link');
            }

            if (! Schema::hasColumn('interviews', 'hr_notes')) {
                $table->text('hr_notes')->nullable()->after('evaluation_path');
            }

            $table->index(['interview_type', 'status']);
        });

        // Normalize existing values for the new workflow statuses.
        \Illuminate\Support\Facades\DB::table('interviews')
            ->where('status', 'pending')
            ->update(['status' => 'scheduled']);
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            if (Schema::hasColumn('interviews', 'interview_type')) {
                $table->dropIndex(['interview_type', 'status']);
                $table->dropColumn(['interview_type', 'evaluation_path', 'hr_notes']);
            }

        });

        \Illuminate\Support\Facades\DB::table('interviews')
            ->where('status', 'scheduled')
            ->update(['status' => 'pending']);
    }
};
