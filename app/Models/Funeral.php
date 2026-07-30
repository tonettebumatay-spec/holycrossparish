<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    use HasFactory;

    protected $table = 'funerals';
    protected $guarded = [];
}