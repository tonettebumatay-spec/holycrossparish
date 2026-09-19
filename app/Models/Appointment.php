<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

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
        'cancellation_reason',
        'is_locked',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'is_locked'        => 'boolean',
    ];
}