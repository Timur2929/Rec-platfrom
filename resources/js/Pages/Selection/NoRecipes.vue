<template>
    <Head title="Нет рецептов" />
    
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Рецептов пока нет</h1>
            <p class="text-gray-600 mb-8">К сожалению, в базе пока нет рецептов.</p>
            <Link
                :href="route('recipes.submit')"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
            >
                Предложить рецепт
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script> 