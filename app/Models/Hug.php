<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hug extends Model
{
    // Add this line to allow mass assignment
    protected $fillable = ['post_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
