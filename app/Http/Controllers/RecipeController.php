<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\RecipeView;
use App\Notifications\RecipeApproved;
use App\Notifications\RecipeRejected;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category', 'ingredients'])
        ->where('status', Recipe::STATUS_APPROVED);

        // Поиск
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('ingredients', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Сортировка
        switch ($request->sort) {
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $recipes = $query->paginate(12);

        // Преобразуем данные рецептов
        $recipes->through(function ($recipe) {
            return [
                'id' => $recipe->id,
                'title' => $recipe->name,
                'description' => $recipe->description,
                'cooking_time' => $recipe->cooking_time,
                'image' => $recipe->image
                    ? (str_starts_with($recipe->image, '/')
                        ? asset($recipe->image)
                        : Storage::url($recipe->image))
                    : asset('/images/placeholder.png'),
                'rating' => $recipe->rating,
                'views' => $recipe->views,
                'user' => [
                    'id' => $recipe->user->id,
                    'full_name' => $recipe->user->full_name,
                    'avatar' => $recipe->user->avatar
                        ? (str_starts_with($recipe->user->avatar, '/')
                            ? asset($recipe->user->avatar)
                            : Storage::url($recipe->user->avatar))
                        : asset('/images/profile/default-avatar.png'),
                ],
                'category' => $recipe->category ? [
                    'id' => $recipe->category->id,
                    'name' => $recipe->category->name
                ] : null
            ];
        });

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'filters' => [
                'search' => $request->search,
                'sort' => $request->sort
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Начало обработки запроса на создание рецепта');
        
        try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.amount' => 'required|string|max:50',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string',
            'steps.*.order' => 'required|integer|min:1',
            'step_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

            \Log::info('Валидация пройдена успешно', ['validated' => $validated]);

            \DB::beginTransaction();

            // Создаем рецепт
            $recipe = new Recipe();
            $recipe->name = $validated['name'];
            $recipe->description = $validated['description'];
            $recipe->category_id = $validated['category_id'];
            $recipe->cooking_time = $validated['cooking_time'];
            $recipe->user_id = auth()->id();
            $recipe->status = Recipe::STATUS_PENDING;
            
            // Сохраняем основное изображение
            if ($request->hasFile('image')) {
                try {
                    \Log::info('Начало загрузки основного изображения', [
                        'original_name' => $request->file('image')->getClientOriginalName(),
                        'size' => $request->file('image')->getSize(),
                        'mime' => $request->file('image')->getMimeType()
                    ]);

                    $path = $request->file('image')->store('recipe-images', 'public');
                    
                    \Log::info('Результат сохранения основного изображения', ['path' => $path]);
                    
                    if (!$path) {
                        throw new \Exception('Не удалось сохранить основное изображение');
                    }
                    $recipe->image = $path;
                } catch (\Exception $e) {
                    \Log::error('Ошибка при загрузке основного изображения', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                \Log::warning('Основное изображение отсутствует в запросе');
            }
            
            $recipe->save();
            \Log::info('Рецепт сохранен', ['recipe_id' => $recipe->id]);

            // Сохраняем ингредиенты
            foreach ($validated['ingredients'] as $ingredientData) {
                $recipe->ingredients()->create([
                    'name' => $ingredientData['name'],
                    'amount' => $ingredientData['amount'],
                    'unit' => $ingredientData['unit']
                ]);
            }
            \Log::info('Ингредиенты сохранены');

            // Сохраняем шаги
            foreach ($validated['steps'] as $index => $stepData) {
                $step = $recipe->steps()->create([
                    'description' => $stepData['description'],
                    'order' => $stepData['order']
                ]);

                // Проверяем наличие изображения для данного шага
                if ($request->hasFile("step_images.{$index}")) {
                    try {
                        \Log::info('Начало загрузки изображения для шага ' . $index, [
                            'original_name' => $request->file("step_images.{$index}")->getClientOriginalName(),
                            'size' => $request->file("step_images.{$index}")->getSize(),
                            'mime' => $request->file("step_images.{$index}")->getMimeType()
                        ]);

                        $path = $request->file("step_images.{$index}")->store('recipe-steps', 'public');
                        
                        \Log::info('Результат сохранения изображения шага', ['step' => $index, 'path' => $path]);
                        
                        if (!$path) {
                            throw new \Exception('Не удалось сохранить изображение шага');
                        }
                        $step->image = $path;
                    $step->save();
                    } catch (\Exception $e) {
                        \Log::error('Ошибка при загрузке изображения шага ' . $index, [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
            }
            \Log::info('Шаги рецепта сохранены');

            \DB::commit();
            \Log::info('Транзакция завершена успешно');

            return redirect()->route('recipes.index')
                ->with('message', 'Рецепт успешно отправлен на модерацию! Он будет опубликован после проверки администратором.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Ошибка при сохранении рецепта', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Произошла ошибка при сохранении рецепта: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe, Request $request)
    {
        // Проверяем, может ли пользователь просматривать этот рецепт
        if ($recipe->status !== Recipe::STATUS_APPROVED && 
            (!auth()->user() || 
            (auth()->user()->id !== $recipe->user_id && 
             auth()->user()->role !== 'admin'))) {
                abort(404);
            }

        // Увеличиваем счетчик просмотров
        if (!$request->user() || $request->user()->id !== $recipe->user_id) {
            // Проверяем, был ли уже просмотр с этого IP и user agent
            $alreadyViewed = RecipeView::where('recipe_id', $recipe->id)
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
                ->exists();

            if (!$alreadyViewed) {
                RecipeView::create([
                    'recipe_id' => $recipe->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                $recipe->increment('views');
            }
        }
            
        $recipe->load(['user', 'category', 'ingredients', 'steps', 'reviews.user']);

        $isFavorited = $request->user() ? $recipe->favoritedBy()->where('user_id', $request->user()->id)->exists() : false;

        return Inertia::render('Recipes/Show', [
            'recipe' => [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description,
                'cooking_time' => $recipe->cooking_time,
                'image' => $recipe->image ? Storage::url($recipe->image) : null,
                'rating' => number_format($recipe->rating, 1),
                'views' => $recipe->views,
                'status' => $recipe->status,
                'rejection_reason' => $recipe->rejection_reason,
                'revision_comment' => $recipe->revision_comment,
                'user' => [
                    'id' => $recipe->user->id,
                    'full_name' => $recipe->user->full_name,
                    'avatar' => $recipe->user->avatar ? Storage::url($recipe->user->avatar) : '/storage/images/profile/default-avatar.png'
                ],
                'category' => $recipe->category ? [
                    'id' => $recipe->category->id,
                    'name' => $recipe->category->name
                ] : null,
                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'amount' => $ingredient->amount,
                        'unit' => $ingredient->unit
                    ];
                }),
                'steps' => $recipe->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'description' => $step->description,
                        'order' => $step->order,
                        'image' => $step->image ? Storage::url($step->image) : null
                    ];
                })->sortBy('order')->values(),
                'reviews' => $recipe->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at,
                        'user' => [
                            'id' => $review->user->id,
                            'full_name' => $review->user->full_name,
                            'avatar' => $review->user->avatar ? Storage::url($review->user->avatar) : '/storage/images/profile/default-avatar.png'
                        ]
                    ];
                })
            ],
            'isFavorited' => $isFavorited
        ]);
    }

    /**
            return Inertia::render('Recipes/Show', [
                'recipe' => $recipeData,
                'isFavorited' => auth()->check() ? auth()->user()->favorites->contains($recipe->id) : false,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('recipes.index')->with('error', 'Не удалось загрузить рецепт');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->id() || !$recipe->isRevision()) {
            abort(403, 'Редактировать можно только свои рецепты на доработке');
        }
        $categories = \App\Models\Category::all();
        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->id() || !$recipe->isRevision()) {
            abort(403, 'Редактировать можно только свои рецепты на доработке');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.amount' => 'required|string|max:50',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string|min:10',
            'steps.*.order' => 'required|integer|min:1',
            'step_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        try {
            \DB::beginTransaction();
            $recipe->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'cooking_time' => $validated['cooking_time'],
                // УБИРАЕМ изменение статуса здесь
                'revision_comment' => null
            ]);
            // Обновить изображение, ингредиенты, шаги (аналогично store)
            if ($request->hasFile('image')) {
                $recipe->image = $request->file('image')->store('recipe-images', 'public');
                $recipe->save();
            }
            $recipe->ingredients()->delete();
            foreach ($validated['ingredients'] as $ingredientData) {
                $recipe->ingredients()->create($ingredientData);
            }
            $recipe->steps()->delete();
            foreach ($validated['steps'] as $index => $stepData) {
                $step = $recipe->steps()->create([
                    'description' => $stepData['description'],
                    'order' => $stepData['order']
                ]);
                if ($request->hasFile("step_images.{$index}")) {
                    $step->image = $request->file("step_images.{$index}")->store('recipe-steps', 'public');
                    $step->save();
                }
            }
            \DB::commit();
            return redirect()->route('recipes.resubmit', $recipe->id);
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении рецепта: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function favorite(Recipe $recipe)
    {
        $user = auth()->user();
        
        if ($user->favorites()->where('recipe_id', $recipe->id)->exists()) {
            $user->favorites()->detach($recipe->id);
            return redirect()->back()->with('status', 'removed'); // Только редирект без данных
        }
    
        $user->favorites()->attach($recipe->id);
        return redirect()->back()->with('status', 'added'); // Только редирект без данных
    }

    public function random()
    {
        $recipe = Recipe::inRandomOrder()->first();
        return redirect()->route('recipes.show', $recipe);
    }

    public function review(Recipe $recipe)
    {
        $recipe->load(['user', 'category', 'ingredients', 'steps']);
        
        $recipeData = [
            'id' => $recipe->id,
            'name' => $recipe->name,
            'description' => $recipe->description,
            'cooking_time' => $recipe->cooking_time,
            'image' => $recipe->image ? Storage::url($recipe->image) : null,
            'status' => $recipe->status,
            'created_at' => $recipe->created_at,
            'user' => [
                'id' => $recipe->user->id,
                'name' => $recipe->user->name,
                'profile_photo_url' => $recipe->user->profile_photo_url
            ],
            'category' => $recipe->category ? [
                'id' => $recipe->category->id,
                'name' => $recipe->category->name
            ] : null,
            'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'amount' => $ingredient->amount,
                    'unit' => $ingredient->unit
                ];
            })->values()->all(),
            'steps' => $recipe->steps->map(function ($step) {
                return [
                    'id' => $step->id,
                    'order' => $step->order,
                    'description' => $step->description,
                    'image' => $step->image ? Storage::url($step->image) : null
                ];
            })->values()->all()
        ];
        
        return Inertia::render('Admin/RecipeReview', [
            'recipe' => $recipeData
        ]);
    }

    public function approve(Recipe $recipe)
    {
        $recipe->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return redirect()->route('admin.recipes.pending')
            ->with('success', 'Рецепт успешно одобрен');
    }

    public function reject(Recipe $recipe, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|min:10'
        ]);

        try {
            \DB::beginTransaction();

        $recipe->update([
                'status' => Recipe::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

            // Отправляем уведомление автору рецепта
            $recipe->user->notify(new RecipeRejected($recipe));

            \DB::commit();

        return redirect()->route('admin.recipes.pending')
                ->with('success', 'Рецепт отклонен, автор уведомлен');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Ошибка при отклонении рецепта: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Произошла ошибка при отклонении рецепта');
        }
    }

    public function pendingRecipes()
    {
        $recipes = Recipe::where('status', 'pending')
            ->with('user')
            ->latest()
            ->paginate(10);
            
        return Inertia::render('Admin/PendingRecipes', [
            'recipes' => $recipes
        ]);
    }

    public function sendToRevision(Recipe $recipe, Request $request)
    {
        $validated = $request->validate([
            'revision_comment' => 'required|min:10'
        ]);

        try {
            \DB::beginTransaction();

            $recipe->update([
                'status' => Recipe::STATUS_REVISION,
                'revision_comment' => $validated['revision_comment'],
            ]);

            // Можно отправить уведомление автору, если нужно
            // $recipe->user->notify(new RecipeRevision($recipe));

            \DB::commit();

            return redirect()->route('admin.recipes.pending')
                ->with('success', 'Рецепт отправлен на доработку');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Ошибка при отправке на доработку: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Произошла ошибка при отправке на доработку');
        }
    }

    public function resubmit(Recipe $recipe, Request $request)
    {
        // Проверка прав
        if (auth()->id() !== $recipe->user_id || !$recipe->isRevision()) {
            abort(403);
        }
    
        // Валидация данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.amount' => 'required|string|max:50',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string|min:10',
            'steps.*.order' => 'required|integer|min:1',
            'step_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        try {
            \DB::beginTransaction();
    
            // Обновляем данные рецепта
            $recipe->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'cooking_time' => $validated['cooking_time'],
                'status' => Recipe::STATUS_PENDING, // Меняем статус
                'revision_comment' => null,
            ]);
    
            // Обновляем изображение, если есть
            if ($request->hasFile('image')) {
                $recipe->image = $request->file('image')->store('recipe-images', 'public');
                $recipe->save();
            }
    
            // Обновляем ингредиенты
            $recipe->ingredients()->delete();
            foreach ($validated['ingredients'] as $ingredientData) {
                $recipe->ingredients()->create($ingredientData);
            }
    
            // Обновляем шаги
            $recipe->steps()->delete();
            foreach ($validated['steps'] as $index => $stepData) {
                $step = $recipe->steps()->create([
                    'description' => $stepData['description'],
                    'order' => $stepData['order']
                ]);
                if ($request->hasFile("step_images.{$index}")) {
                    $step->image = $request->file("step_images.{$index}")->store('recipe-steps', 'public');
                    $step->save();
                }
            }
    
            \DB::commit();
    
            return redirect()->route('profile.show')
                ->with('success', 'Рецепт отправлен на повторную модерацию!');
    
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
}
