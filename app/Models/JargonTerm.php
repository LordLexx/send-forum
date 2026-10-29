<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JargonTerm extends Model
{
    protected $fillable = [
        'term',
        'full_form',
        'definition',
        'extra_content',
        'slug',
        'status',
        'user_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
