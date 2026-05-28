<?php

declare(strict_types=1);

use App\Enums\RecruitmentStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('applications')
            ->where('recruitment_stage', 'ADMIN_REVIEW')
            ->update([
                'recruitment_stage' => RecruitmentStage::ADMINISTRASI->value,
                'status' => RecruitmentStage::ADMINISTRASI->toInt(),
            ]);

        DB::table('application_stage_logs')
            ->where('stage', 'ADMIN_REVIEW')
            ->update(['stage' => RecruitmentStage::ADMINISTRASI->value]);
    }

    public function down(): void
    {
        DB::table('applications')
            ->where('recruitment_stage', RecruitmentStage::ADMINISTRASI->value)
            ->update([
                'recruitment_stage' => 'ADMIN_REVIEW',
                'status' => 1,
            ]);

        DB::table('application_stage_logs')
            ->where('stage', RecruitmentStage::ADMINISTRASI->value)
            ->update(['stage' => 'ADMIN_REVIEW']);
    }
};
