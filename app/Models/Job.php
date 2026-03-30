<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JobLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Job extends Model
{
    protected $fillable = [
        'title',
        'description',
        'requirements',
        'benefits',
        'level',
        'is_active',
        'closed_at',
        'created_by',
        'department_id',
        'site_id',
        'ptk_id',
    ];

    protected function casts(): array
    {
        return [
            'level' => JobLevel::class,
            'is_active' => 'boolean',
            'closed_at' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function images(): HasMany
    {
        $relation = $this->hasMany(JobImage::class);
        $relation->orderBy('sort_order');

        return $relation;
    }

    public function featuredImage(): HasOne
    {
        return $this->hasOne(JobImage::class)->where('is_featured', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNested(function ($q) {
            $q->whereNull('closed_at')->orWhere('closed_at', '>=', now());
        });
    }
}