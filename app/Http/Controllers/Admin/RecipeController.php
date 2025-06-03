<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\RecipeRejected;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('user')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Admin/Recipes/Index', [
            'recipes' => $recipes
        ]);
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['user', 'category', 'ingredients', 'steps']);
        
        return Inertia::render('Admin/Recipes/Show', [
            'recipe' => $recipe
        ]);
    }

    public function approve(Recipe $recipe)
    {
        DB::transaction(function () use ($recipe) {
            $recipe->update([
                'status' => 'approved',
                'approved_at' => now(),
                'rejection_reason' => null,
                'revision_comment' => null
            ]);
        });

        return back()->with('success', 'Рецепт успешно одобрен');
    }

    public function reject(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:3|max:1000'
        ]);

        DB::transaction(function () use ($recipe, $validated) {
            $recipe->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'revision_comment' => null
            ]);
        });

        return back()->with('success', 'Рецепт отклонен');
    }

    public function revision(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'revision_comment' => 'required|string|min:3|max:1000'
        ]);

        DB::transaction(function () use ($recipe, $validated) {
            $recipe->update([
                'status' => 'revision',
                'revision_comment' => $validated['revision_comment'],
                'rejection_reason' => null
            ]);
        });

        return back()->with('success', 'Рецепт отправлен на доработку');
    }
} 