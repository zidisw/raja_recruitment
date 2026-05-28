<?php

declare(strict_types=1);

namespace App\Enums;

enum RecruitmentStage: string
{
    case APPLIED = 'APPLIED';
    case ADMINISTRASI = 'ADMINISTRASI';
    case HR_INTERVIEW = 'HR_INTERVIEW';
    case USER_INTERVIEW = 'USER_INTERVIEW';
    case OFFERING = 'OFFERING';
    case PSYCHOTEST = 'PSYCHOTEST';
    case MCU = 'MCU';
    case ONBOARDING = 'ONBOARDING';
    case HIRED = 'HIRED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::APPLIED => 'Applied',
            self::ADMINISTRASI => 'Administrasi',
            self::HR_INTERVIEW => 'HR Interview',
            self::USER_INTERVIEW => 'User Interview',
            self::OFFERING => 'Offering',
            self::PSYCHOTEST => 'Psychotest',
            self::MCU => 'MCU',
            self::ONBOARDING => 'Onboarding',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
        };
    }

    public function notificationLabel(): string
    {
        return match ($this) {
            self::APPLIED => 'Lamaran Terkirim',
            self::ADMINISTRASI => 'Lolos Administrasi',
            self::HR_INTERVIEW => 'Interview HR',
            self::USER_INTERVIEW => 'Interview User',
            self::OFFERING => 'Offering Letter',
            self::PSYCHOTEST => 'Psikotes',
            self::MCU => 'Medical Check Up',
            self::ONBOARDING => 'Onboarding',
            self::HIRED => 'Diterima',
            self::REJECTED => 'Tidak Lolos',
        };
    }

    /**
     * Get the next stage in the forward-only pipeline.
     */
    public function next(): ?self
    {
        return match ($this) {
            self::APPLIED => self::ADMINISTRASI,
            self::ADMINISTRASI => self::HR_INTERVIEW,
            self::HR_INTERVIEW => self::USER_INTERVIEW,
            self::USER_INTERVIEW => self::OFFERING,
            self::OFFERING => self::PSYCHOTEST,
            self::PSYCHOTEST => self::MCU,
            self::MCU => self::ONBOARDING,
            self::ONBOARDING => self::HIRED,
            self::HIRED, self::REJECTED => null,
        };
    }

    /**
     * Whether this stage is a terminal (end) state.
     */
    public function isTerminal(): bool
    {
        return $this === self::HIRED || $this === self::REJECTED;
    }

    /**
     * Backward-compatible integer representation (maps to legacy ApplicationStatus values).
     */
    public function toInt(): int
    {
        return match ($this) {
            self::APPLIED => 0,
            self::ADMINISTRASI => 1,
            self::HR_INTERVIEW => 2,
            self::USER_INTERVIEW => 3,
            self::OFFERING => 5,
            self::PSYCHOTEST => 4,
            self::MCU => 6,
            self::ONBOARDING => 7,
            self::HIRED => 8,
            self::REJECTED => 99,
        };
    }

    /**
     * Create a RecruitmentStage from a legacy ApplicationStatus integer value.
     */
    public static function fromLegacyInt(int $value): self
    {
        return match ($value) {
            0 => self::APPLIED,
            1 => self::ADMINISTRASI,
            2 => self::HR_INTERVIEW,
            3 => self::USER_INTERVIEW,
            4 => self::PSYCHOTEST,
            5 => self::OFFERING,
            6 => self::MCU,
            7 => self::ONBOARDING,
            8 => self::HIRED,
            99 => self::REJECTED,
            default => self::APPLIED,
        };
    }

    /**
     * All pipeline stages (excluding REJECTED) in order.
     */
    public static function pipelineStages(): array
    {
        return [
            self::APPLIED,
            self::ADMINISTRASI,
            self::HR_INTERVIEW,
            self::USER_INTERVIEW,
            self::OFFERING,
            self::PSYCHOTEST,
            self::MCU,
            self::ONBOARDING,
            self::HIRED,
        ];
    }

    /**
     * Stages relevant to email templates (pipeline stages except APPLIED and REJECTED).
     */
    public static function emailTemplateStages(): array
    {
        return array_filter(
            self::pipelineStages(),
            fn (self $s) => $s !== self::APPLIED
        );
    }
}
