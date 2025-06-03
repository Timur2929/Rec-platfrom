<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Category;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    // Профиль пользователя
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Создание рецептов
    Route::get('/recipes/submit', function () {
        return Inertia::render('Recipes/Submit', [
            'categories' => Category::all()
        ]);
    })->name('recipes.submit');

    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::post('/recipes/{recipe}/favorite', [RecipeController::class, 'favorite'])->name('recipes.favorite');

    // Отзывы
    Route::post('/recipes/{recipe}/reviews', [ReviewController::class, 'store'])->name('recipes.reviews.store');
    Route::put('/recipes/{recipe}/reviews/{review}', [ReviewController::class, 'update'])->name('recipes.reviews.update');
    Route::delete('/recipes/{recipe}/reviews/{review}', [ReviewController::class, 'destroy'])->name('recipes.reviews.destroy');

    // Избранное
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Редактирование рецепта
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');

    // Категории
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

    Route::post('/recipes/{recipe}/resubmit', [RecipeController::class, 'resubmit'])->name('recipes.resubmit');
});

// Публичные маршруты
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/random', [RecipeController::class, 'random'])->name('recipes.random');
Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

Route::get('/selection', [SelectionController::class, 'random'])->name('selection.random');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Маршруты для модерации рецептов
    Route::post('/admin/recipes/{recipe}/approve', [\App\Http\Controllers\Admin\RecipeController::class, 'approve'])->name('admin.recipes.approve');
    Route::post('/admin/recipes/{recipe}/reject', [\App\Http\Controllers\Admin\RecipeController::class, 'reject'])->name('admin.recipes.reject');
    Route::post('/admin/recipes/{recipe}/revision', [\App\Http\Controllers\Admin\RecipeController::class, 'revision'])->name('admin.recipes.revision');
});


require __DIR__.'/auth.php';
