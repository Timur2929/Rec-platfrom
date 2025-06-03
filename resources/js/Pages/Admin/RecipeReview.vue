<template>
    <AppLayout>
        <Head :title="'Модерация рецепта: ' + recipe.name" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Заголовок и статус -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Модерация рецепта</h1>
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 rounded-full text-sm" 
                                  :class="{
                                    'bg-yellow-100 text-yellow-800': recipe.status === 'pending',
                                    'bg-green-100 text-green-800': recipe.status === 'approved',
                                    'bg-red-100 text-red-800': recipe.status === 'rejected'
                                  }">
                                {{ statusText }}
                            </span>
                            <div class="flex space-x-2">
                                <button 
                                    @click="approveRecipe"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">
                                    Одобрить
                                </button>
                                <button 
                                    @click="showRejectModal = true"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50">
                                    Отклонить
                                </button>
                                <button
                                    @click="showRevisionModal = true"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 disabled:opacity-50">
                                    На доработку
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Информация о рецепте -->
                    <div class="space-y-8">
                        <!-- Основная информация -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h2 class="text-xl font-semibold mb-4">{{ recipe.name }}</h2>
                                <p class="text-gray-600">{{ recipe.description }}</p>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500">Время приготовления: {{ recipe.cooking_time }}</p>
                                    <p class="text-sm text-gray-500">Категория: {{ recipe.category.name }}</p>
                                    <p class="text-sm text-gray-500">Автор: {{ recipe.user.name }}</p>
                                    <p class="text-sm text-gray-500">Дата отправки: {{ formatDate(recipe.created_at) }}</p>
                                </div>
                            </div>
                            <div>
                                <img 
                                    :src="recipe.image" 
                                    :alt="recipe.name"
                                    class="w-full h-64 object-cover rounded-lg shadow-md"
                                />
                            </div>
                        </div>

                        <!-- Ингредиенты -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Ингредиенты</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <ul class="space-y-2">
                                    <li v-for="ingredient in recipe.ingredients" :key="ingredient.id" class="flex justify-between">
                                        <span>{{ ingredient.name }}</span>
                                        <span class="text-gray-600">{{ ingredient.amount }} {{ ingredient.unit }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Шаги приготовления -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Шаги приготовления</h3>
                            <div class="space-y-6">
                                <div v-for="step in recipe.steps" :key="step.id" class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">
                                            {{ step.order }}
                                        </div>
                                        <div class="flex-grow">
                                            <p>{{ step.description }}</p>
                                            <img 
                                                v-if="step.image"
                                                :src="step.image"
                                                :alt="'Шаг ' + step.order"
                                                class="mt-4 max-w-md rounded-lg shadow-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для отклонения рецепта -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Укажите причину отклонения рецепта
                </h3>
                <div class="mb-4">
                    <textarea
                        v-model="form.rejection_reason"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        rows="4"
                        placeholder="Опишите, почему рецепт отклонен..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showRejectModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Отмена
                    </button>
                    <button
                        @click="rejectRecipe"
                        :disabled="!form.rejection_reason || form.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                    >
                        Отклонить рецепт
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Модальное окно для доработки рецепта -->
        <Modal :show="showRevisionModal" @close="showRevisionModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Укажите замечания для доработки рецепта
                </h3>
                <div class="mb-4">
                    <textarea
                        v-model="revisionForm.revision_comment"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        rows="4"
                        placeholder="Опишите, что нужно исправить..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showRevisionModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Отмена
                    </button>
                    <button
                        @click="sendToRevision"
                        :disabled="!revisionForm.revision_comment || revisionForm.processing"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 disabled:opacity-50"
                    >
                        Отправить на доработку
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    recipe: {
        type: Object,
        required: true
    }
});

const showRejectModal = ref(false);
const showRevisionModal = ref(false);

const form = useForm({
    rejection_reason: ''
});

const revisionForm = useForm({
    revision_comment: ''
});

const statusText = computed(() => {
    switch (props.recipe.status) {
        case 'pending':
            return 'На рассмотрении';
        case 'approved':
            return 'Одобрен';
        case 'rejected':
            return 'Отклонен';
        default:
            return 'Неизвестно';
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const approveRecipe = () => {
    form.post(route('admin.recipes.approve', props.recipe.id), {
        onSuccess: () => {
            // Успешно одобрено
        }
    });
};

const rejectRecipe = () => {
    if (!form.rejection_reason) return;

    form.post(route('admin.recipes.reject', props.recipe.id), {
        onSuccess: () => {
            showRejectModal.value = false;
        }
    });
};

const sendToRevision = () => {
    if (!revisionForm.revision_comment) return;
    revisionForm.post(route('admin.recipes.revision', props.recipe.id), {
        onSuccess: () => {
            showRevisionModal.value = false;
        }
    });
};
</script> 