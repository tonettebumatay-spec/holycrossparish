<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAvailability extends Model
{
    protected $fillable = [
        'sacrament_type', 'available_date', 'start_time', 'end_time', 'max_slots', 'is_active'
    ];

    protected $casts = [
        'available_date' => 'date',
        'start_time'     => 'datetime:H:i',
        'end_time'       => 'datetime:H:i',
        'is_active'      => 'boolean',
    ];
}
