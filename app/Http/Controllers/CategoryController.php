<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['recipes' => function($query) {
            $query->where('status', Recipe::STATUS_APPROVED);
        }])->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image' => $category->image ? Storage::url($category->image) : null,
                'recipes_count' => $category->recipes_count,
            ];
        });
        return Inertia::render('Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function show(Category $category)
    {
        $recipes = Recipe::where('category_id', $category->id)
            ->where('status', Recipe::STATUS_APPROVED)
            ->with('user')
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->name,
                    'description' => $recipe->description,
                    'image' => $recipe->image ? Storage::url($recipe->image) : null,
                    'cooking_time' => $recipe->cooking_time,
                    'rating' => number_format($recipe->rating, 1),
                    'views' => $recipe->views,
                    'user' => [
                        'first_name' => $recipe->user->first_name,
                        'last_name' => $recipe->user->last_name,
                        'profile_photo_url' => $recipe->user->profile_photo_url
                    ]
                ];
            });

        return Inertia::render('Categories/Show', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image' => $category->image ? Storage::url($category->image) : null,
            ],
            'recipes' => $recipes
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        $category = new Category($validated);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }
        $category->save();
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? null;
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }
        $category->save();
        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Категория удалена');
    }
} 