<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communion extends Model
{
    protected $table = 'communions';

    protected $fillable = [
        'book_number',
        'page_number',
        'line_number',
        'first_name',
        'last_name',
        'communion_date',
        'residence',
        'minister_name',
        'baptism_date',
        'place_of_baptism',
    ];
}