<template>
    <AppLayout>
    <Head title="Рецепты на модерации" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-6">Рецепты на модерации</h2>

                        <div v-if="recipes.data.length > 0">
                            <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Название
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Автор
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Дата отправки
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="recipe in recipes.data" :key="recipe.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                        {{ recipe.name }}
                                                </div>
                                    </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img 
                                                            :src="recipe.user.profile_photo_url" 
                                                            :alt="recipe.user.name"
                                                            class="h-10 w-10 rounded-full"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                        {{ recipe.user.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                    </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                        {{ formatDate(recipe.created_at) }}
                                                </div>
                                    </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link 
                                            :href="route('admin.recipes.review', recipe.id)"
                                                    class="text-blue-600 hover:text-blue-900 mr-4"
                                        >
                                            Просмотреть
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                            <div class="mt-6">
                        <Pagination :links="recipes.links" />
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <p class="text-gray-500">Нет рецептов на модерации</p>
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
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    recipes: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script> 