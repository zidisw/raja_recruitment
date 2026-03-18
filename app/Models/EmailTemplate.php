<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecruitmentStage;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'stage',
        'job_level',
        'subject',
        'body',
    ];

    protected function casts(): array
    {
        return [
            'stage' => RecruitmentStage::class,
        ];
    }
}