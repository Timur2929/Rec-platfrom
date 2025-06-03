<template>
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="relative h-56">
            <img 
                :src="recipe.image || '/images/recipe-placeholder.jpg'" 
                :alt="recipe.title"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
            >
            <div class="absolute top-0 right-0 mt-3 mr-3">
                <span class="bg-white/90 backdrop-blur-sm text-gray-700 px-3 py-1.5 rounded-full text-sm font-medium shadow-sm">
                    {{ recipe.cooking_time }}
                </span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                <h3 class="text-xl font-semibold text-white">
                    {{ recipe.title }}
                </h3>
            </div>
        </div>

        <div class="p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ recipe.category.name }}
                </span>
                <span class="text-sm text-gray-500">
                    {{ recipe.created_at }}
                </span>
            </div>

            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ recipe.description }}</p>

            <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-gray-100">
                <img 
                    :src="recipe.user.profile_photo_url || '/storage/images/profile/default-avatar.png'" 
                    :alt="recipe.user.first_name + ' ' + recipe.user.last_name"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm"
                    @error="$event.target.src = '/storage/images/profile/default-avatar.png'"
                >
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900">
                        {{ recipe.user.first_name }} {{ recipe.user.last_name }}
                    </span>
                    <span class="text-xs text-gray-500">
                        Автор рецепта
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <Link 
                    :href="route('recipes.show', recipe.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Просмотреть рецепт
                </Link>
                <div class="flex gap-3">
                    <button 
                        @click="$emit('approve', recipe.id)"
                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Одобрить
                    </button>
                    <button 
                        @click="$emit('reject', recipe.id)"
                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Отклонить
                    </button>
                </div>
                <button
                    @click="$emit('revision', recipe.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    На доработку
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    recipe: {
        type: Object,
        required: true
    }
});

defineEmits(['approve', 'reject', 'revision']);
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style> 