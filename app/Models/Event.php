<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Make sure 'slug' is in this array!
    protected $fillable = [
        'title', 'slug', 'description', 'category', 'location', 'location_type',
        'address', 'map_link', 'meeting_link', 'passcode',
        'start_date', 'end_date', 'status', 'image', 'user_id',
        'is_fathers_hub', // 👈 NEW
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_fathers_hub' => 'boolean', // 👈 NEW
    ];

    // Tell Laravel to use 'slug' instead of 'id' for route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}
