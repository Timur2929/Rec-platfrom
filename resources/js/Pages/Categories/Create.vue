<template>
    <AppLayout>
        <Head title="Создать категорию" />
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-2xl font-bold mb-6">Создать категорию</h1>
                <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Название категории</label>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Изображение</label>
                        <input
                            type="file"
                            id="image"
                            @input="onFileChange"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.image }"
                        />
                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                        <div v-if="imagePreview" class="mt-2">
                            <img :src="imagePreview" :alt="form.name" class="w-32 h-32 object-cover rounded" />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <Link
                            :href="route('categories.index')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                        >
                            Отмена
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                            :disabled="form.processing"
                        >
                            Создать
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    name: '',
    image: null
});

const imagePreview = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    imagePreview.value = file ? URL.createObjectURL(file) : null;
};

const submit = () => {
    if (form.image) {
        form.post(route('categories.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                imagePreview.value = null;
            }
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => {
                form.reset();
                imagePreview.value = null;
            }
        });
    }
};
</script> 