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
        // ✅ BAGONG FIELDS
        'groom_dob', 'groom_pob', 'groom_father', 'groom_mother', 'groom_address', 'groom_picture',
        'bride_dob', 'bride_pob', 'bride_father', 'bride_mother', 'bride_address', 'bride_picture',
        'publication_start', 'publication_end',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'banns_date' => 'date',
        'expires_at' => 'date',
        'publication_start' => 'date',
        'publication_end' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function updateExpiredStatus()
    {
        self::where('status', 'active')
            ->where('expires_at', '<', now()->toDateString())
            ->update(['status' => 'expired']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}