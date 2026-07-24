<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    use HasFactory;

    protected $table = 'funerals';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'deceased_name',
        'residence',
        'marital_status',
        'spouse_name',
        'death_date',
        'age_at_death',
        'burial_date',
        'cause_of_death',
        'sacraments_received',
        'cemetery_name',
        'minister_name',
        'remarks',
        'status',
        'cancellation_reason',
        'is_locked',
    ];

    protected $casts = [
        'death_date' => 'date',
        'burial_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}