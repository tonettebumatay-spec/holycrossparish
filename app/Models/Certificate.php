<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'certificate_type',
        'status',
        'request_date',
        'contact_number',
        'appointment_date',
        'appointment_time',
        'details',
        'user_id',
        'email',
        'cancellation_reason',
        'is_locked',
    ];

    protected $casts = [
        'request_date'     => 'date',
        'appointment_date' => 'date',
        'is_locked'        => 'boolean',
    ];
}