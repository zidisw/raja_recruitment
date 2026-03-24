<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecruitmentStage;
use App\Observers\ApplicationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(ApplicationObserver::class)]
class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'status',
        'recruitment_stage',
        'hr_notes',
        'interviewer_notes',
        'stage_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'recruitment_stage' => RecruitmentStage::class,
            'stage_updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Keep the legacy integer `status` column in sync from `recruitment_stage`.
        static::saving(function (self $application): void {
            if ($application->isDirty('recruitment_stage') && $application->recruitment_stage instanceof RecruitmentStage) {
                $application->status = $application->recruitment_stage->toInt();
            }
        });
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function stageLogs(): HasMany
    {
        $relation = $this->hasMany(ApplicationStageLog::class);
        $relation->orderBy('created_at');

        return $relation;
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function hrInterview(): HasOne
    {
        return $this->hasOne(Interview::class)->where('interview_type', 'HR Interview');
    }

    public function userInterview(): HasOne
    {
        return $this->hasOne(Interview::class)->where('interview_type', 'User Interview');
    }

    public function offeringLetter(): HasOne
    {
        return $this->hasOne(OfferingLetter::class);
    }

    public function psychotest(): HasOne
    {
        return $this->hasOne(Psychotest::class);
    }

    public function mcu(): HasOne
    {
        return $this->hasOne(Mcu::class);
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class);
    }

    public function canAdvance(): bool
    {
        return ! $this->recruitment_stage->isTerminal();
    }

    public function nextStage(): ?RecruitmentStage
    {
        return $this->recruitment_stage->next();
    }
}