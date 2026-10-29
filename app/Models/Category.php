<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'description', 'status', 'make_priority'];

    protected $casts = [
        'make_priority' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
