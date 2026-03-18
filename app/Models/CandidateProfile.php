<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'marital_status',
        'address_ktp',
        'address_domicile',
        'whatsapp',
        'linkedin_url',
        'ktp_path',
        'photo_path',
        'portfolio_path',
        'certificate_path',
        'paklaring_path'
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}