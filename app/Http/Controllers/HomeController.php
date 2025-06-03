<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tastyRecipes = Recipe::where('status', Recipe::STATUS_APPROVED)
            ->with(['category', 'user'])
            ->orderBy('rating', 'desc')
            ->take(3)
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->name,
                    'description' => $recipe->description,
                    'cooking_time' => $recipe->cooking_time,
                    'image' => $recipe->image ? \Storage::url($recipe->image) : null,
                    'rating' => $recipe->rating,
                    'views' => $recipe->views,
                    'user' => [
                        'first_name' => $recipe->user->first_name,
                        'last_name' => $recipe->user->last_name,
                        'profile_photo_url' => $recipe->user->avatar ? Storage::url($recipe->user->avatar) : '/storage/images/profile/default-avatar.png'
                    ],
                    'category' => $recipe->category ? [
                        'id' => $recipe->category->id,
                        'name' => $recipe->category->name
                    ] : null
                ];
            });


        return Inertia::render('Welcome', [
            'user' => $request->user(),
            'popularCategories' => Category::take(3)->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? \Storage::url($category->image) : null,
                ];
            }),
            'tastyRecipes' => $tastyRecipes,
        ]);
    }
} 