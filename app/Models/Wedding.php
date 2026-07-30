<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    protected $table = 'weddings';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'year',
        'month_day',

        'groom_name',
        'groom_age',
        'groom_status',
        'groom_residence',
        'groom_parents',
        'groom_parents_residence',

        'bride_name',
        'bride_age',
        'bride_status',
        'bride_residence',
        'bride_parents',
        'bride_parents_residence',
    ];
}