<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    public const STATUS_NEW        = 'new';
    public const STATUS_READ       = 'read';
    public const STATUS_RESPONDED = 'responded';
    public const STATUS_ARCHIVED  = 'archived';

    public const TOPIC_GENERAL  = 'general';
    public const TOPIC_EVENT    = 'event';
    public const TOPIC_STEERING = 'steering';

    protected $fillable = [
        'name', 'email', 'topic', 'subject', 'message',
        'consent', 'status', 'admin_notes', 'admin_reply',
        'replied_at', 'ip_address',
        'is_verified', 'verification_token', 'verified_at',
    ];

    protected $casts = [
        'consent'     => 'boolean',
        'replied_at'  => 'datetime',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function getTopicLabelAttribute(): string
    {
        return match ($this->topic) {
            self::TOPIC_GENERAL  => 'General Enquiry',
            self::TOPIC_EVENT    => 'Event Booking',
            self::TOPIC_STEERING => 'Joining Steering Committee',
            default              => ucfirst($this->topic),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW        => 'New',
            self::STATUS_READ       => 'Read',
            self::STATUS_RESPONDED => 'Responded',
            self::STATUS_ARCHIVED  => 'Archived',
            default                 => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW        => 'bg-red-100 text-red-700',
            self::STATUS_READ       => 'bg-yellow-100 text-yellow-700',
            self::STATUS_RESPONDED => 'bg-green-100 text-green-700',
            self::STATUS_ARCHIVED  => 'bg-gray-100 text-gray-700',
            default                 => 'bg-gray-100 text-gray-700',
        };
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }
}
