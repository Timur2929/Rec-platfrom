<template>
    <AdminLayout>
        <Head :title="recipe.title" />

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">{{ recipe.title }}</h1>
                    <Link 
                        :href="route('admin.recipes.index')" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg"
                    >
                        Назад к списку
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Статус -->
                    <div class="mb-6">
                        <span 
                            :class="{
                                'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full': true,
                                'bg-green-100 text-green-800': recipe.status === 'approved',
                                'bg-yellow-100 text-yellow-800': recipe.status === 'pending',
                                'bg-red-100 text-red-800': recipe.status === 'rejected',
                                'bg-blue-100 text-blue-800': recipe.status === 'revision'
                            }"
                        >
                            {{ getStatusText(recipe.status) }}
                        </span>
                    </div>

                    <!-- Основная информация -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Информация о рецепте</h2>
                            <div class="space-y-2">
                                <p><span class="font-medium">Автор:</span> {{ recipe.user.name }}</p>
                                <p><span class="font-medium">Категория:</span> {{ recipe.category?.name || 'Не указана' }}</p>
                                <p><span class="font-medium">Время приготовления:</span> {{ recipe.cooking_time }} мин.</p>
                                <p><span class="font-medium">Сложность:</span> {{ recipe.difficulty }}</p>
                                <p><span class="font-medium">Порции:</span> {{ recipe.servings }}</p>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Статистика</h2>
                            <div class="space-y-2">
                                <p><span class="font-medium">Просмотры:</span> {{ recipe.views }}</p>
                                <p><span class="font-medium">Рейтинг:</span> {{ recipe.rating }}</p>
                                <p><span class="font-medium">Дата создания:</span> {{ formatDate(recipe.created_at) }}</p>
                                <p><span class="font-medium">Дата обновления:</span> {{ formatDate(recipe.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Изображение -->
                    <div class="mb-6">
                        <img 
                            :src="recipe.image" 
                            :alt="recipe.title" 
                            class="w-full h-64 object-cover rounded-lg"
                        />
                    </div>

                    <!-- Описание -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Описание</h2>
                        <p class="text-gray-700">{{ recipe.description }}</p>
                    </div>

                    <!-- Ингредиенты -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Ингредиенты</h2>
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="ingredient in recipe.ingredients" :key="ingredient.id">
                                {{ ingredient.name }} - {{ ingredient.amount }} {{ ingredient.unit }}
                            </li>
                        </ul>
                    </div>

                    <!-- Шаги приготовления -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Шаги приготовления</h2>
                        <ol class="list-decimal list-inside space-y-4">
                            <li v-for="step in recipe.steps" :key="step.id" class="text-gray-700">
                                {{ step.description }}
                            </li>
                        </ol>
                    </div>

                    <!-- Действия модерации -->
                    <div v-if="recipe.status === 'pending'" class="flex justify-end space-x-4">
                        <button 
                            @click="approveRecipe"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg"
                        >
                            Одобрить
                        </button>
                        <button 
                            @click="rejectRecipe"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg"
                        >
                            Отклонить
                        </button>
                        <button 
                            @click="requestRevision"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg"
                        >
                            На доработку
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusText = (status) => {
    const statusMap = {
        'pending': 'На рассмотрении',
        'approved': 'Одобрено',
        'rejected': 'Отклонено',
        'revision': 'На доработке'
    };
    return statusMap[status] || status;
};

const approveRecipe = () => {
    if (confirm('Вы уверены, что хотите одобрить этот рецепт?')) {
        router.post(route('admin.recipes.approve', props.recipe.id));
    }
};

const rejectRecipe = () => {
    if (confirm('Вы уверены, что хотите отклонить этот рецепт?')) {
        router.post(route('admin.recipes.reject', props.recipe.id));
    }
};

const requestRevision = () => {
    if (confirm('Вы уверены, что хотите отправить этот рецепт на доработку?')) {
        router.post(route('admin.recipes.revision', props.recipe.id));
    }
};
</script> 