<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communion extends Model
{
    use HasFactory;

    protected $table = 'communions';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'candidate_name',
        'first_name',
        'last_name',
        'communion_date',
        'minister_name',
        'coordinator_name',
        'residence',
        'baptism_date',
        'place_of_baptism',
        'remarks',
        'status',
        'cancellation_reason',
        'is_locked',
    ];

    protected $casts = [
        'communion_date' => 'date',
        'baptism_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}