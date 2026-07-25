<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    protected $table = 'funerals';

    protected $fillable = [
        'category',
        'deceased_name',
        'residence',
        'remarks',
        'book_number',
        'page_number',
        'line_number',
    ];
}