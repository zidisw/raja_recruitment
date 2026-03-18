<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateEducation extends Model
{
    protected $fillable = [
        'user_id',
        'degree',
        'institution_name',
        'major',
        'start_year',
        'end_year',
        'gpa'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}