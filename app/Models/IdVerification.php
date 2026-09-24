<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'id_type',
        'id_number',
        'full_name',
        'birthdate',
        'address',
        'id_picture_path',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}