<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'original_filename',
        'file_type',
        'external_url',
        'status',
        'user_id',
        'is_fathers_hub', // 👈 NEW
    ];

    protected $casts = [
        'is_fathers_hub' => 'boolean', // 👈 NEW
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFathersHub(Builder $query): Builder
    {
        return $query->where('is_fathers_hub', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', ['draft', 'published']);
    }
}
