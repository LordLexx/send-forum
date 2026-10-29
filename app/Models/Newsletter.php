<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'preview_text',
        'blocks',
        'style_config',
        'status',
        'recipient_group',
        'sender_name',
        'reply_to',
        'track_opens',
        'track_clicks',
        'created_by',
        'sent_at',
    ];

    protected $casts = [
        'blocks'       => 'array',
        'style_config' => 'array',
        'track_opens'  => 'boolean',
        'track_clicks' => 'boolean',
        'sent_at'      => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }
}
