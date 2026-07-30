<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    protected $table = 'funerals';

    protected $fillable = [
        'reference_location',
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
        'user_id',
        'email',
    ];
}