<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    protected $table = 'weddings';

    protected $fillable = [
        'category',
        'groom_name',
        'bride_name',
        'remarks',
        'book_number',
        'page_number',
        'line_number',
        'year',
        'month_day',
    ];
}