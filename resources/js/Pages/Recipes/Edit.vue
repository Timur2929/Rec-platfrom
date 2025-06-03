<template>
    <AppLayout>
        <Head title="Редактировать рецепт" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Редактировать рецепт</h2>

                    <div v-if="recipe.status === 'revision' && recipe.revision_comment" 
                        class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                        <div class="flex items-start">
                            <span class="mr-3 text-yellow-500 text-2xl">⚠️</span>
                            <div>
                                <h3 class="font-semibold text-yellow-700">Требуется доработка</h3>
                                <p class="text-yellow-800 mt-1">{{ recipe.revision_comment }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="recipe.status === 'rejected' && recipe.rejection_reason" 
                        class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                        <div class="flex items-start">
                            <span class="mr-3 text-red-500 text-2xl">❌</span>
                            <div>
                                <h3 class="font-semibold text-red-700">Рецепт отклонён</h3>
                                <p class="text-red-800 mt-1">{{ recipe.rejection_reason }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="recipe.status !== 'revision'" class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded">
                        Редактировать можно только рецепт, который находится на доработке.
                    </div>

                    <form v-else @submit.prevent="submitForModeration" class="space-y-6">
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
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition"
                                :disabled="form.processing || isSubmitting">
                                <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ isSubmitting ? 'Отправка...' : 'Отправить на повторную модерацию' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    recipe: Object,
    categories: Array
});

const form = useForm({
    name: props.recipe.name,
    description: props.recipe.description,
    cooking_time: props.recipe.cooking_time,
    category_id: props.recipe.category_id,
    image: null,
    ingredients: props.recipe.ingredients ? props.recipe.ingredients.map(i => ({ ...i })) : [{ name: '', amount: '', unit: '' }],
    steps: props.recipe.steps ? props.recipe.steps.map(s => ({ description: s.description, order: s.order })) : [{ description: '', order: 1 }],
    step_images: {}
});

const previewImage = ref(props.recipe.image ? (props.recipe.image.startsWith('http') ? props.recipe.image : `/storage/${props.recipe.image}`) : null);
const stepPreviews = ref({});
const stepImageInputs = ref({});
const isSubmitting = ref(false);

onMounted(() => {
    if (props.recipe.steps) {
        props.recipe.steps.forEach((step, idx) => {
            if (step.image) {
                stepPreviews.value[idx] = step.image.startsWith('http') ? step.image : `/storage/${step.image}`;
            }
        });
    }
});

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
    form.steps.forEach((step, i) => {
        step.order = i + 1;
    });
};

const submitForModeration = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('description', form.description);
    formData.append('cooking_time', form.cooking_time);
    formData.append('category_id', form.category_id);

    if (form.image) {
        formData.append('image', form.image);
    }

    form.ingredients.forEach((ing, idx) => {
        formData.append(`ingredients[${idx}][name]`, ing.name);
        formData.append(`ingredients[${idx}][amount]`, ing.amount);
        formData.append(`ingredients[${idx}][unit]`, ing.unit);
    });

    form.steps.forEach((step, idx) => {
        formData.append(`steps[${idx}][description]`, step.description);
        formData.append(`steps[${idx}][order]`, step.order);
        if (form.step_images[idx]) {
            formData.append(`step_images[${idx}]`, form.step_images[idx]);
        }
    });

    // ✅ Отправляем на /resubmit, а не /update
    axios.post(route('recipes.resubmit', props.recipe.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        window.location = route('recipes.show', props.recipe.id);
    })
    .catch(error => {
        if (error.response?.data?.errors) {
            form.errors = error.response.data.errors;
        }
    })
    .finally(() => {
        isSubmitting.value = false;
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