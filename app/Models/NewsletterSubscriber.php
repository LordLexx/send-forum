<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'is_verified', 'verification_token'];

    protected $casts = [
        'is_verified' => 'boolean',
    ];
}