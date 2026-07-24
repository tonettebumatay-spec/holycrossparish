<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;

    protected $table = 'confirmations';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'candidate_name',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'confirmation_date',
        'minister_name',
        'sponsor_name',
        'sponsors',  // Added this to match the database column
        'age',
        'birthplace',
        'parents_residence',
        'year',
        'month_day',
        'remarks',
        'status',
        'cancellation_reason',
        'is_locked',
    ];

    protected $casts = [
        'confirmation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}