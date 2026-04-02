<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    protected $fillable = [
        'application_id',
        'joining_date',
        'onboarding_status',
        'travel_ticket_number',
        'travel_ticket_notes',
        'travel_ticket_sent_at',
        'onsite_date',
        'onsite_location',
        'onsite_notes',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'travel_ticket_sent_at' => 'datetime',
            'onsite_date' => 'date',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
