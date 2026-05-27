<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the table explicitly first using raw query if necessary
        // DB::statement('ALTER TABLE application_stage_logs MODIFY stage VARCHAR(50) NOT NULL');
        Schema::table('application_stage_logs', function (Blueprint $table) {
            $table->string('stage', 50)->change();
        });

        // 2. Map existing integer strings ('0', '1', ...) to proper enum strings
        $mapping = [
            '0' => \App\Enums\RecruitmentStage::APPLIED->value,
            '1' => \App\Enums\RecruitmentStage::HR_INTERVIEW->value, // mappings for the removed admin review
            '2' => \App\Enums\RecruitmentStage::HR_INTERVIEW->value,
            '3' => \App\Enums\RecruitmentStage::USER_INTERVIEW->value,
            '4' => \App\Enums\RecruitmentStage::PSYCHOTEST->value,
            '5' => \App\Enums\RecruitmentStage::OFFERING->value,
            '6' => \App\Enums\RecruitmentStage::MCU->value,
            '7' => \App\Enums\RecruitmentStage::HIRED->value,
            '99' => \App\Enums\RecruitmentStage::REJECTED->value,
        ];
        foreach ($mapping as $old => $new) {
            DB::statement('UPDATE application_stage_logs SET stage = ? WHERE stage = ?', [$new, $old]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_stage_logs', function (Blueprint $table) {
            // It's not safe to revert string to int if strings like 'HR_INTERVIEW' exist, so leave it or handle it roughly
            $table->string('stage', 255)->change();
        });
    }
};
