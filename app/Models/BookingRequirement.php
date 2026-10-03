<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'sacrament_type',
        'requirement_name',
        'description',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: only active requirements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter by sacrament.
     */
    public function scopeForSacrament($query, $sacrament)
    {
        return $query->where('sacrament_type', strtolower($sacrament));
    }

    /**
     * Get ordered requirements.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}