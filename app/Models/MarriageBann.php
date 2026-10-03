<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarriageBann extends Model
{
    use HasFactory;

    protected $fillable = [
        'groom_name',
        'bride_name',
        'wedding_date',
        'banns_date',
        'status',
        'expires_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'banns_date' => 'date',
        'expires_at' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Auto-update status based on expiry.
     */
    public static function updateExpiredStatus()
    {
        self::where('status', 'active')
            ->where('expires_at', '<', now()->toDateString())
            ->update(['status' => 'expired']);
    }

    /**
     * Scope: active banns only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: public banns (active + not expired).
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now()->toDateString());
            });
    }
}