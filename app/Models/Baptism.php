<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baptism extends Model
{
    protected $table = 'baptisms';

    protected $fillable = [
        'category',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'legitimacy',
        'remarks',
        'book_number',
        'page_number',
        'line_number',
    ];
}