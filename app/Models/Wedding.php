<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    use HasFactory;

    protected $table = 'weddings';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'groom_name',
        'groom_age',
        'groom_status',
        'groom_father',
        'groom_mother',
        'groom_parents',  // Added to match database column
        'groom_parents_residence',  // Added to match database column
        'groom_residence',
        'bride_name',
        'bride_age',
        'bride_status',
        'bride_father',
        'bride_mother',
        'bride_parents',  // Added to match database column
        'bride_parents_residence',  // Added to match database column
        'bride_residence',
        'wedding_date',
        'minister_name',
        'witness_1',
        'witness_2',
        'year',
        'month_day',
        'remarks',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}