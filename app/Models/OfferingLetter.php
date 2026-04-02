<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferingLetter extends Model
{
    protected $fillable = [
        'application_id',
        'offer_date',
        'file_path',
        'status',
        'candidate_notes',
        'signed_file_path',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'offer_date' => 'date',
            'signed_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
