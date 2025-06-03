<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        $user = auth()->user();
        
        // Получаем URL'ы для изображений
        $avatarUrl = $user->avatar 
            ? Storage::url($user->avatar) 
            : '/storage/images/profile/stas.png';
            
        $coverUrl = $user->cover_image 
            ? Storage::url($user->cover_image) 
            : '/storage/images/profile/KANEKI KEN SAD.png';
        
        $data = [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'avatar_url' => $avatarUrl,
                'cover_image' => $coverUrl
            ],
            'pendingRecipes' => []
        ];

        // Для обычного пользователя — все его рецепты
        $data['user']['recipes'] = $user->recipes()->with('category')->orderByDesc('created_at')->get()->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'title' => $recipe->name,
                'description' => $recipe->description,
                'cooking_time' => $recipe->cooking_time,
                'image' => $recipe->image ? Storage::url($recipe->image) : '/storage/recipes/placeholder.jpg',
                'status' => $recipe->status,
                'category' => $recipe->category ? $recipe->category->name : null,
                'rejection_reason' => $recipe->rejection_reason,
                'revision_comment' => $recipe->revision_comment,
                'rating' => number_format($recipe->rating, 1),
            ];
        });

        // Для администратора — заявки на рассмотрении
        if ($user->role === 'admin') {
            $pendingRecipes = Recipe::where('status', 'pending')
                ->with(['user', 'category'])
                ->get()
                ->map(function ($recipe) {
                    return [
                        'id' => $recipe->id,
                        'title' => $recipe->name,
                        'description' => $recipe->description,
                        'category' => [
                            'id' => $recipe->category->id,
                            'name' => $recipe->category->name
                        ],
                        'cooking_time' => $recipe->cooking_time,
                        'image' => $recipe->image ? Storage::url($recipe->image) : '/storage/recipes/placeholder.jpg',
                        'author' => $recipe->user->first_name . ' ' . $recipe->user->last_name,
                        'rating' => number_format($recipe->rating, 1),
                        'views' => $recipe->views,
                        'status' => $recipe->status,
                        'rejection_reason' => $recipe->rejection_reason,
                        'revision_comment' => $recipe->revision_comment,
                        'show_url' => route('recipes.show', $recipe->id)
                    ];
                });
            $data['pendingRecipes'] = $pendingRecipes;
        }

        return Inertia::render('Profile/Show', $data);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $request->user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        \Log::info('Profile update request data:', [
            'request_data' => $request->all(),
            'has_avatar' => $request->hasFile('avatar'),
            'has_cover' => $request->hasFile('cover_image'),
            'current_avatar' => $user->avatar,
            'current_cover' => $user->cover_image,
            'files' => $request->allFiles()
        ]);

        if ($request->hasFile('avatar')) {
            \Log::info('Processing avatar upload:', [
                'file_name' => $request->file('avatar')->getClientOriginalName(),
                'file_size' => $request->file('avatar')->getSize(),
                'mime_type' => $request->file('avatar')->getMimeType()
            ]);
            
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatar = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatar;
        }

        if ($request->hasFile('cover_image')) {
            \Log::info('Processing cover image upload:', [
                'file_name' => $request->file('cover_image')->getClientOriginalName(),
                'file_size' => $request->file('cover_image')->getSize(),
                'mime_type' => $request->file('cover_image')->getMimeType()
            ]);
            
            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }
            $coverImage = $request->file('cover_image')->store('cover_images', 'public');
            $user->cover_image = $coverImage;
        }

        $user->fill($request->validated());

        \Log::info('User data before save:', [
            'user_data' => $user->toArray(),
            'is_dirty' => $user->isDirty(),
            'dirty_attributes' => $user->getDirty()
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        \Log::info('User data after save:', [
            'user_data' => $user->fresh()->toArray()
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
