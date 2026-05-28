<?php

declare(strict_types=1);

use App\Enums\RecruitmentStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('stage', 50)->change();
        });

        foreach ($this->legacyStageMap() as $legacy => $stage) {
            DB::table('email_templates')
                ->where('stage', (string) $legacy)
                ->update(['stage' => $stage]);
        }
    }

    public function down(): void
    {
        foreach ($this->legacyStageMap() as $legacy => $stage) {
            DB::table('email_templates')
                ->where('stage', $stage)
                ->update(['stage' => (string) $legacy]);
        }

        Schema::table('email_templates', function (Blueprint $table) {
            $table->integer('stage')->change();
        });
    }

    /**
     * @return array<int, string>
     */
    private function legacyStageMap(): array
    {
        return [
            0 => RecruitmentStage::APPLIED->value,
            1 => RecruitmentStage::ADMINISTRASI->value,
            2 => RecruitmentStage::HR_INTERVIEW->value,
            3 => RecruitmentStage::USER_INTERVIEW->value,
            4 => RecruitmentStage::PSYCHOTEST->value,
            5 => RecruitmentStage::OFFERING->value,
            6 => RecruitmentStage::MCU->value,
            7 => RecruitmentStage::ONBOARDING->value,
            8 => RecruitmentStage::HIRED->value,
            99 => RecruitmentStage::REJECTED->value,
        ];
    }
};
