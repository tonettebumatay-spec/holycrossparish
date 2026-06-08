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
    ];

    protected $casts = [
        'request_date' => 'date',
    ];
}

