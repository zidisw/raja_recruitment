<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ptk extends Model
{
    protected $table = 'ptk';

    protected $fillable = [
        'nomor_ptk',
        'department',
        'posisi',
        'jumlah_kebutuhan',
        'alasan_permintaan',
        'tanggal_permintaan',
        'status',
        'created_by',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_permintaan' => 'date',
            'jumlah_kebutuhan' => 'integer',
        ];
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
