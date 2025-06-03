<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SelectionController extends Controller
{
    public function random()
    {
        // Получаем случайный рецепт из одобренных
        $recipe = Recipe::where('status', Recipe::STATUS_APPROVED)
            ->inRandomOrder()
            ->with(['user', 'ingredients', 'steps' => function($query) {
                $query->orderBy('order');
            }])
            ->first();

        if (!$recipe) {
            return Inertia::render('Selection/NoRecipes');
        }
        // Проверяем, добавлен ли рецепт в избранное текущим пользователем
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favorites()->where('recipe_id', $recipe->id)->exists();
        }

        // Форматируем ингредиенты в читаемый вид
        $formattedIngredients = $recipe->ingredients->map(function($ingredient) {
            return sprintf(
                "%s - %s%s",
                $ingredient->name,
                $ingredient->amount,
                $ingredient->unit
            );
        })->join("\n");

        // Форматируем шаги приготовления
        $formattedSteps = $recipe->steps->map(function($step) {
            return [
                'order' => $step->order,
                'description' => $step->description,
                'image' => $step->image ? (
                    str_starts_with($step->image, '/') 
                        ? asset($step->image) 
                        : Storage::url($step->image)
                ) : null
            ];
        })->values()->all();

        // Готовим данные пользователя
        $user = $recipe->user;
        $author = [
            'first_name' => $user->first_name ?? 'Аноним',
            'last_name' => $user->last_name ?? '',
            'avatar' => $user->avatar
                ? (str_starts_with($user->avatar, '/')
                    ? asset($user->avatar)
                    : Storage::url($user->avatar))
                : '/storage/images/profile/default-avatar.png',
        ];

        // Обработка пути к изображению рецепта
        $imagePath = $recipe->image;
        \Log::info('Recipe image path:', ['path' => $imagePath]);
        
        if ($imagePath) {
            // Если путь начинается с 'images/', используем asset()
            if (str_starts_with($imagePath, 'images/')) {
                $image = asset($imagePath);
                \Log::info('Using asset() for images/ path:', ['url' => $image]);
            } 
            // Если путь начинается с 'recipe-images/', используем Storage::url()
            else if (str_starts_with($imagePath, 'recipe-images/')) {
                $image = Storage::url($imagePath);
                \Log::info('Using Storage::url() for recipe-images/ path:', ['url' => $image]);
            }
            // В остальных случаях пробуем через Storage::url()
            else {
                $image = Storage::url($imagePath);
                \Log::info('Using Storage::url() for other path:', ['url' => $image]);
            }
        } else {
            $image = asset('/images/placeholder.png');
            \Log::info('Using placeholder image:', ['url' => $image]);
        }

        \Log::info('Final image URL:', ['url' => $image]);

        return Inertia::render('Selection/Random', [
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->name,
                'description' => $recipe->description ?? 'Описание отсутствует',
                'ingredients' => $formattedIngredients,
                'steps' => $formattedSteps,
                'image' => $image,
                'cooking_time' => $recipe->cooking_time . '',
                'rating' => $recipe->rating ?? 0.0,
                'user' => $author,
            ],
            'initialIsFavorite' => $isFavorite
        ]);
    }
} 