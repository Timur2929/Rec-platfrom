<template>
    <AppLayout>
        <Head title="Отправить рецепт" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Отправить рецепт на модерацию</h2>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Основная информация -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Название рецепта</label>
                                <input 
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Описание</label>
                                <textarea 
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Время приготовления</label>
                                <input 
                                    v-model="form.cooking_time"
                                    type="text"
                                    placeholder="например: 1 час 30 минут"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                />
                                <p v-if="form.errors.cooking_time" class="mt-1 text-sm text-red-600">{{ form.errors.cooking_time }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Категория</label>
                                <select 
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                >
                                    <option value="">Выберите категорию</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Фото блюда</label>
                                <div class="mt-1 flex items-center">
                                    <input 
                                        type="file"
                                        @change="handleImageUpload"
                                        accept="image/*"
                                        class="hidden"
                                        ref="imageInput"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="$refs.imageInput.click()"
                                        class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300"
                                    >
                                        Выбрать фото
                                    </button>
                                    <img v-if="previewImage" :src="previewImage" class="ml-4 h-20 w-20 object-cover rounded-md" />
                                </div>
                                <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                            </div>
                        </div>

                        <!-- Ингредиенты -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Ингредиенты</h3>
                                <button 
                                    type="button"
                                    @click="addIngredient"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Добавить ингредиент
                                </button>
                            </div>

                            <div v-for="(ingredient, index) in form.ingredients" :key="index" class="flex gap-4 items-start">
                                <div class="flex-1">
                                    <input 
                                        v-model="ingredient.name"
                                        type="text"
                                        placeholder="Название"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        required
                                    />
                                </div>
                                <div class="w-24">
                                    <input 
                                        v-model="ingredient.amount"
                                        type="text"
                                        placeholder="Кол-во"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        required
                                    />
                                </div>
                                <div class="w-24">
                                    <input 
                                        v-model="ingredient.unit"
                                        type="text"
                                        placeholder="Ед. изм."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        required
                                    />
                                </div>
                                <button 
                                    type="button"
                                    @click="removeIngredient(index)"
                                    class="mt-1 text-red-600 hover:text-red-800"
                                    :disabled="form.ingredients.length === 1"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Шаги приготовления -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Шаги приготовления</h3>
                                <button 
                                    type="button"
                                    @click="addStep"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Добавить шаг
                                </button>
                            </div>

                            <div v-for="(step, index) in form.steps" :key="index" class="space-y-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-medium">Шаг {{ step.order }}</h4>
                                    <button 
                                        type="button"
                                        @click="removeStep(index)"
                                        class="text-red-600 hover:text-red-800"
                                        :disabled="form.steps.length === 1"
                                    >
                                        Удалить шаг
                                    </button>
                                </div>

                                <div>
                                    <textarea 
                                        v-model="step.description"
                                        rows="3"
                                        placeholder="Описание шага"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        required
                                    ></textarea>
                                </div>

                                <div>
                                    <input 
                                        type="file"
                                        @change="(e) => handleStepImageUpload(e, index)"
                                        accept="image/*"
                                        class="hidden"
                                        :ref="el => { if (el) stepImageInputs[index] = el }"
                                    />
                                    <button
                                        type="button"
                                        @click="stepImageInputs[index]?.click()"
                                        class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300"
                                    >
                                        Добавить фото к шагу
                                    </button>
                                    <img v-if="stepPreviews[index]" :src="stepPreviews[index]" class="mt-2 h-20 w-20 object-cover rounded-md" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition"
                                :disabled="form.processing || isSubmitting"
                            >
                                <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ isSubmitting ? 'Отправка...' : 'Отправить на модерацию' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: '',
    description: '',
    cooking_time: '',
    category_id: '',
    image: null,
    ingredients: [{ name: '', amount: '', unit: '' }],
    steps: [{ description: '', order: 1 }],
    step_images: {}
});

const previewImage = ref(null);
const stepPreviews = ref({});
const stepImageInputs = ref({});
const isSubmitting = ref(false);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleStepImageUpload = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        form.step_images[index] = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            stepPreviews.value[index] = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addIngredient = () => {
    form.ingredients.push({ name: '', amount: '', unit: '' });
};

const removeIngredient = (index) => {
    form.ingredients.splice(index, 1);
};

const addStep = () => {
    const newOrder = form.steps.length + 1;
    form.steps.push({
        description: '',
        order: newOrder
    });
};

const removeStep = (index) => {
    form.steps.splice(index, 1);
    delete stepPreviews.value[index];
    delete form.step_images[index];
    
    // Обновляем порядок шагов
    form.steps.forEach((step, i) => {
        step.order = i + 1;
    });
};

const submit = () => {
    if (isSubmitting.value) return;
    
    isSubmitting.value = true;

    // Проверяем обязательные поля
    if (!form.image) {
        alert('Пожалуйста, добавьте основное фото рецепта');
        isSubmitting.value = false;
        return;
    }

    if (!form.category_id) {
        alert('Пожалуйста, выберите категорию');
        isSubmitting.value = false;
        return;
    }

    // Проверяем ингредиенты
    if (!form.ingredients.every(ing => ing.name && ing.amount && ing.unit)) {
        alert('Пожалуйста, заполните все поля для каждого ингредиента');
        isSubmitting.value = false;
        return;
    }

    // Проверяем шаги
    if (!form.steps.every(step => step.description)) {
        alert('Пожалуйста, добавьте описание для каждого шага');
        isSubmitting.value = false;
        return;
    }

    // Отправляем данные
    form.post(route('recipes.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // Очищаем форму после успешной отправки
            form.reset();
            previewImage.value = null;
            stepPreviews.value = {};
            form.step_images = {};
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
            // Прокручиваем к первой ошибке
            const firstError = document.querySelector('.text-red-600');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
};
</script>

<style scoped>
.ingredient-row {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.step-container {
    border: 1px solid #e2e8f0;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.5rem;
}
</style> 