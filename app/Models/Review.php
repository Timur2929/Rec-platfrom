<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating',
        'comment',
        'user_id',
        'recipe_id'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Получить рецепт, к которому относится отзыв
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Получить пользователя, оставившего отзыв
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 