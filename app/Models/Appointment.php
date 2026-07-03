<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_name', 
        'service_type', 
        'appointment_date', 
        'contact_number', 
        'details', 
        'status'
    ];
}