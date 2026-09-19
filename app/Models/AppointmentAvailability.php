<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAvailability extends Model
{
    protected $table = 'appointment_availabilities';

    protected $fillable = [
        'sacrament_type',
        'available_date',
        'start_time',
        'end_time',
        'max_slots',
        'is_active',
    ];

    protected $casts = [
        'available_date' => 'date',
        'is_active'      => 'boolean',
    ];
}