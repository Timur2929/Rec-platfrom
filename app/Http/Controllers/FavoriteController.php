<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    /**
     * Display a listing of favorite recipes.
     */
    public function index(Request $request)
    {
        $favorites = Auth::user()->favorites()
            ->with(['user', 'category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->name,
                    'description' => $recipe->description,
                    'cooking_time' => $recipe->cooking_time,
                    'image' => $recipe->image ? Storage::url($recipe->image) : null,
                    'rating' => round($recipe->reviews_avg_rating ?? 0, 1),
                    'reviews_count' => $recipe->reviews_count,
                    'category' => $recipe->category ? $recipe->category->name : null,
                    'user' => [
                        'first_name' => $recipe->user->first_name,
                        'last_name' => $recipe->user->last_name,
                        'profile_photo_url' => $recipe->user->avatar ? Storage::url($recipe->user->avatar) : '/storage/images/profile/default-avatar.png'
                    ]
                ];
            });
        
        return Inertia::render('Favorites/Index', [
            'favorites' => $favorites
        ]);
    }
} 