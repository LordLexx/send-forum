<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'title', 'body', 'media', 'replies_count', 'hugs_count', 'status'];

    protected $casts = [
        'media' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function hugs(): HasMany
    {
        return $this->hasMany(Hug::class);
    }

    // Add this inside the Post class
    public function comments()
    {
        // Only top-level comments (where parent_id is null)
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    // Add this inside the Post class
    public function saves()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }
}
