<template>
    <Head :title="recipe.title" />
    
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Главное изображение -->
                <div class="relative h-96">
                    <img 
                        v-if="recipe.image"
                        :src="recipe.image" 
                        :alt="recipe.title"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="no-image flex items-center justify-center w-full h-full bg-gray-200 text-gray-500">
                        Нет изображения
                    </div>
                    <div class="absolute top-4 right-4 bg-white px-4 py-2 rounded-full shadow-md">
                        <span class="text-gray-600">{{ recipe.cooking_time }}</span>
                    </div>
                </div>

                <!-- Информация о рецепте -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ recipe.title }}</h1>
                        <div class="flex items-center">
                            <div class="flex items-center mr-4">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="ml-1 text-gray-600">{{ recipe.rating || '0.0' }}</span>
                            </div>
                            <div class="flex items-center">
                                <img 
                                    :src="recipe.user.avatar" 
                                    :alt="recipe.user.first_name + ' ' + recipe.user.last_name"
                                    class="w-8 h-8 rounded-full object-cover"
                                />
                                <span class="ml-2 text-gray-600">{{ recipe.user.first_name }} {{ recipe.user.last_name }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-8">{{ recipe.description || 'Описание отсутствует' }}</p>

                    <!-- Ингредиенты -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ингредиенты:</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="whitespace-pre-line">{{ recipe.ingredients || 'Нет данных' }}</div>
                        </div>
                    </div>

                    <!-- Инструкции -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Инструкции:</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div v-if="recipe.steps && recipe.steps.length" class="space-y-6">
                                <div v-for="step in recipe.steps" :key="step.order" class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-4">
                                            {{ step.order }}
                                        </div>
                                        <div class="flex-grow">
                                            <p class="text-gray-700 mb-4">{{ step.description }}</p>
                                            <img 
                                                v-if="step.image" 
                                                :src="step.image" 
                                                :alt="'Шаг ' + step.order"
                                                class="rounded-lg w-full max-w-2xl h-auto"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-gray-500">
                                Инструкции отсутствуют
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="flex justify-between items-center">
                        <button 
                            @click="getRandomRecipe"
                            class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            Следующий рецепт
                        </button>
                        <button 
                            @click="toggleFavorite"
                            class="flex items-center text-gray-600 hover:text-yellow-500 transition-colors"
                            :disabled="processing"
                            :class="{'text-yellow-500': isFavorite}">
                            <svg class="w-6 h-6" :class="{'fill-yellow-500': isFavorite}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="ml-2">{{ isFavorite ? 'Удалить из избранного' : 'В избранное' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    recipe: Object,
    initialIsFavorite: Boolean
});

const processing = ref(false);
const isFavorite = ref(props.initialIsFavorite);

const toggleFavorite = async () => {
    if (processing.value) return;
    
    processing.value = true;
    try {
        isFavorite.value = !isFavorite.value; // Оптимистичное обновление
        
        await axios.post(route('recipes.favorite', { recipe: props.recipe.id }), {}, {
            headers: {
                'Accept': 'application/json', // Явно указываем ожидаемый тип ответа
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
    } catch (error) {
        isFavorite.value = !isFavorite.value; // Откат при ошибке
        console.error('Ошибка:', error);
    } finally {
        processing.value = false;
    }
};

const getRandomRecipe = () => {
    router.visit(route('selection.random'), {
        preserveScroll: true
    });
};
</script> 