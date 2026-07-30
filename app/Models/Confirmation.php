<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $table = 'confirmations';

    protected $fillable = [
        'book_number',
        'page_number',
        'line_number',
        'year',
        'month_day',
        'first_name',
        'last_name',
        'age',
        'birthplace',
        'father_name',
        'mother_name',
        'parents_residence',
        'sponsors',
        'minister_name',
    ];
}