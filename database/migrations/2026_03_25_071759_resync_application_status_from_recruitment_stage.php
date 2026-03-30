<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Re-sync the legacy `status` integer column from the `recruitment_stage` string column.
     *
     * Previously MCU and ONBOARDING both incorrectly mapped to 6.
     * New correct mapping:
     *   APPLIED        => 0
     *   HR_INTERVIEW   => 2
     *   USER_INTERVIEW => 3
     *   PSYCHOTEST     => 4
     *   OFFERING       => 5
     *   MCU            => 6
     *   ONBOARDING     => 7  (was 6, duplicate of MCU)
     *   HIRED          => 8  (was 7, which was ONBOARDING's old value)
     *   REJECTED       => 99
     */
    public function up(): void
    {
        DB::table('applications')->update([
            'status' => DB::raw("CASE recruitment_stage
                WHEN 'APPLIED'        THEN 0
                WHEN 'HR_INTERVIEW'   THEN 2
                WHEN 'USER_INTERVIEW' THEN 3
                WHEN 'PSYCHOTEST'     THEN 4
                WHEN 'OFFERING'       THEN 5
                WHEN 'MCU'            THEN 6
                WHEN 'ONBOARDING'     THEN 7
                WHEN 'HIRED'          THEN 8
                WHEN 'REJECTED'       THEN 99
                ELSE status
            END"),
        ]);
    }

    /**
     * Reverse: restore the old (buggy) mapping.
     * Note: this will re-introduce the duplicate 6 for both MCU and ONBOARDING.
     */
    public function down(): void
    {
        DB::table('applications')->update([
            'status' => DB::raw("CASE recruitment_stage
                WHEN 'APPLIED'        THEN 0
                WHEN 'HR_INTERVIEW'   THEN 2
                WHEN 'USER_INTERVIEW' THEN 3
                WHEN 'PSYCHOTEST'     THEN 4
                WHEN 'OFFERING'       THEN 5
                WHEN 'MCU'            THEN 6
                WHEN 'ONBOARDING'     THEN 6
                WHEN 'HIRED'          THEN 7
                WHEN 'REJECTED'       THEN 99
                ELSE status
            END"),
        ]);
    }
};
