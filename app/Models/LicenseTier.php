<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseTier extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'status',
        'order'
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Add ordering scope
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
