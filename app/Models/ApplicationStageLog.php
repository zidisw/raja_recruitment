<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecruitmentStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'stage',
        'decision',
        'notes',
        'decided_by',
    ];

    protected function casts(): array
    {
        return [
            'stage' => RecruitmentStage::class,
            'created_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
