<template>
    <Head :title="category.name" />
    <AppLayout>
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <div v-if="category.image" class="mb-4">
                            <img :src="category.image" :alt="category.name" class="w-40 h-32 object-cover rounded shadow" />
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ category.name }}</h1>
                        <p class="mt-2 text-gray-600">{{ category.description }}</p>
                    </div>
                    <button
                        v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'"
                        @click="deleteCategory"
                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 ml-4 h-12"
                    >
                        Удалить категорию
                    </button>
                </div>

                <div v-if="recipes.length === 0" class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="text-xl text-gray-500">В этой категории пока нет рецептов</p>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recipe in recipes" :key="recipe.id" class="recipe-card bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="relative recipe-image-wrapper">
                            <img 
                                :src="recipe.image" 
                                :alt="recipe.title" 
                                class="w-full h-48 object-cover"
                                @error="handleImageError"
                            >
                            <div class="absolute top-0 right-0 mt-2 mr-2">
                                <span class="bg-white/90 backdrop-blur-sm text-gray-700 px-3 py-1 rounded-full text-sm font-medium shadow-sm">
                                    {{ recipe.cooking_time }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                    <Link :href="route('recipes.show', recipe.id)">
                                        {{ recipe.title }}
                                    </Link>
                                </h3>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ recipe.description }}</p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <img 
                                        :src="recipe.user.profile_photo_url || '/storage/images/profile/default-avatar.png'" 
                                        :alt="recipe.user.first_name + ' ' + recipe.user.last_name"
                                        class="w-8 h-8 rounded-full object-cover border border-gray-200"
                                        @error="$event.target.src = '/storage/images/profile/default-avatar.png'"
                                    >
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ recipe.user.first_name }} {{ recipe.user.last_name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12.585l-4.243 2.415 1.098-4.72L3.172 6.85l4.95-.42L10 2l1.878 4.43 4.95.42-3.683 3.43 1.098 4.72L10 12.585z"/>
                                        </svg>
                                        {{ recipe.rating || '0.0' }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ recipe.views }}
                                    </div>
                                </div>
                            </div>

                            <Link
                                :href="route('recipes.show', recipe.id)"
                                class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                Перейти к рецепту
                                <svg class="ml-2 -mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    recipes: {
        type: Array,
        required: true
    }
});

const handleImageError = (event) => {
    event.target.src = '/images/placeholder.png';
};

const deleteCategory = () => {
    if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
        Inertia.delete(route('categories.destroy', props.category.id));
    }
};
</script>

<style scoped>
.recipe-card {
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
}

.recipe-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.recipe-image-wrapper {
    overflow: hidden;
}

.recipe-image-wrapper img {
    transition: transform 0.3s ease;
}

.recipe-card:hover .recipe-image-wrapper img {
    transform: scale(1.05);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style> 