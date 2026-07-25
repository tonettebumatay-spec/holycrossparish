<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $table = 'confirmations';

    protected $fillable = [
        'category',
        'candidate_name',
        'father_name',
        'mother_name',
        'parents_residence',
        'remarks',
        'book_number',
        'page_number',
        'line_number',
    ];
}