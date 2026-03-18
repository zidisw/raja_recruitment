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
        'status',
    ];

    protected function casts(): array
    {
        return [
            'offer_date' => 'date',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
