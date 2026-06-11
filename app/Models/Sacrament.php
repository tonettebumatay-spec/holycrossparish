<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacrament extends Model
{
    use HasFactory;

    // Mass assignable fields based on your paper notes
    protected $fillable = [
        'type', 
        'name', 
        'year', 
        'book_number', 
        'page_number', 
        'qr_code_token'
    ];
}