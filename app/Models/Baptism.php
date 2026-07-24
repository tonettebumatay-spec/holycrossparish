<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baptism extends Model
{
    protected $fillable = [
        'category', 'book_number', 'page_number', 'line_number', 
        'first_name', 'last_name', 'legitimacy', 'birth_date', 
        'birth_place', 'father_name', 'father_birthplace', 
        'mother_maiden_name', 'mother_birthplace', 'residence', 
        'baptism_date', 'minister_name', 'godfather', 'godmother', 'remarks',
        'status',
        'cancellation_reason',
        'is_locked',
    ];
}