<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('interviewer_notes')->nullable()->after('hr_notes');
            $table->timestamp('stage_updated_at')->nullable()->after('interviewer_notes');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interviewer_notes', 'stage_updated_at']);
        });
    }
};
