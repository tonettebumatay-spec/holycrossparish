<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'receipt_number',
        'payment_method',
        'amount',
        'reference_number',
        'full_name',
        'sacrament_type',
        'appointment_date',
        'appointment_time',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'appointment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}