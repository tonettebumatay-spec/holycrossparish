<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communion extends Model
{
    protected $table = 'communions';

    protected $fillable = [
        'category',
        'candidate_name',
        'residence',
        'remarks',
        'book_number',
        'page_number',
        'line_number',
    ];
}