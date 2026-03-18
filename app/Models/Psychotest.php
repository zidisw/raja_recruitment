<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Psychotest extends Model
{
    protected $fillable = [
        'application_id',
        'test_date',
        'result',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
