<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'otp_code',
        'purpose',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new OTP code for a user.
     */
    public static function generate($userId, $email, $purpose = 'login')
    {
        // Invalidate old OTPs for this purpose
        self::where('user_id', $userId)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate new 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $userId,
            'email' => $email,
            'otp_code' => $otp,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);
    }

    /**
     * Verify an OTP code.
     */
    public static function verify($userId, $otpCode, $purpose = 'login')
    {
        $otp = self::where('user_id', $userId)
            ->where('otp_code', $otpCode)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            $otp->update(['is_used' => true]);
            return true;
        }

        return false;
    }
}