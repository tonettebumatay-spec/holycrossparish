<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay',
        'date',
        'time',
        'description',
        'status', // This column exists in your migration
    ];

    // If you have additional columns, add them here.
}