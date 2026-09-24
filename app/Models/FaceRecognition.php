<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceRecognition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'certificate_id',
        'face_picture_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}