<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'name',
        'amount',
        'unit'
    ];

    /**
     * Получить рецепт, к которому относится ингредиент
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
} 