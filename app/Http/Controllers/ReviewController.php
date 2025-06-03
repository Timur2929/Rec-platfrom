<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        // Проверяем, не оставил ли пользователь уже отзыв
        $existingReview = Review::where('recipe_id', $recipe->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->withErrors(['rating' => 'Вы уже оставили отзыв на этот рецепт']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000'
        ]);

        $review = new Review($validated);
        $review->user_id = Auth::id();
        $recipe->reviews()->save($review);

        // Обновляем средний рейтинг рецепта
        $this->updateRecipeRating($recipe);

        return back()->with('success', 'Отзыв успешно добавлен');
    }

    public function update(Request $request, Recipe $recipe, Review $review)
    {
        // Проверяем, принадлежит ли отзыв текущему пользователю
        if ($review->user_id !== Auth::id()) {
            return back()->withErrors(['rating' => 'Вы не можете редактировать этот отзыв']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000'
        ]);

        $review->update($validated);

        // Обновляем средний рейтинг рецепта
        $this->updateRecipeRating($recipe);

        return back()->with('success', 'Отзыв успешно обновлен');
    }

    public function destroy(Recipe $recipe, Review $review)
    {
        // Проверяем, принадлежит ли отзыв текущему пользователю
        if ($review->user_id !== Auth::id()) {
            return back()->withErrors(['rating' => 'Вы не можете удалить этот отзыв']);
        }

        $review->delete();

        // Обновляем средний рейтинг рецепта
        $this->updateRecipeRating($recipe);

        return back()->with('success', 'Отзыв успешно удален');
    }

    private function updateRecipeRating(Recipe $recipe)
    {
        $averageRating = $recipe->reviews()->avg('rating');
        $recipe->update(['rating' => round($averageRating, 1)]);
    }
}
