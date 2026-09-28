<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMongoPayment extends Model
{
    use HasFactory;

    // ✅ I-override ang default table name
    protected $table = 'paymongo_payments';

    protected $fillable = [
        'user_id',
        'paymongo_id',
        'checkout_session_id',
        'reference_number',
        'amount',
        'currency',
        'payment_method',
        'status',
        'qr_code_url',
        'payment_intent_status',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}