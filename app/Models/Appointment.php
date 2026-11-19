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
        // 👇 GINAGAWA NATING STRING PARA HINDI MAG-ADJUST NG ORAS
        'expires_at' => 'string', 
        'expired_at' => 'string',
        'expiry_reminder_sent' => 'boolean',
    ];

    // ==================== EXPIRY METHODS ====================

    public function isExpired(): bool
    {
        return $this->expires_at && \Carbon\Carbon::parse($this->expires_at)->isPast();
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expires_at) return false;
        $expiresAt = \Carbon\Carbon::parse($this->expires_at);
        
        return $expiresAt->isFuture() && $expiresAt->diffInHours(now()) < 1;
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->expires_at) return null;
        $expiresAt = \Carbon\Carbon::parse($this->expires_at);
        
        if ($expiresAt->isPast()) return null;

        return $expiresAt->diffForHumans(now(), true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now()->format('Y-m-d H:i:s'));
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }
}