<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baptism extends Model
{
    use HasFactory;

    protected $table = 'baptisms';

    // Tanggapin ang lahat ng fields para hindi ma-filter out ng controller
    protected $guarded = [];
}