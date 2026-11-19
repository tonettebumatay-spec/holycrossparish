<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'service_type',
        'appointment_date',
        'appointment_time',
        'contact_number',
        'details',
        'status',
        'user_id',
        'email',
        'expires_at',
        'expired_at',
        'expiry_reminder_sent',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'expires_at' => 'datetime',
        'expired_at' => 'datetime',
        'expiry_reminder_sent' => 'boolean',
    ];

    // ==================== EXPIRY METHODS ====================

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expires_at
            && $this->expires_at->isFuture()
            && $this->expires_at->diffInHours(now()) < 1;
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return null;
        }

        return $this->expires_at->diffForHumans(now(), true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }
}