<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mcu extends Model
{
    protected $fillable = [
        'application_id',
        'mcu_date',
        'result',
        'notes',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'mcu_date' => 'date',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
