<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementUnit extends Model
{
    protected $fillable = [
        'name',
        'short_name'
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
} 