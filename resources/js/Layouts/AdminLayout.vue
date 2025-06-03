<template>
    <div class="min-h-screen bg-gray-100">
        <template v-if="!minimal">
            <!-- Навигация -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Логотип -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route('admin.articles.index')" class="text-xl font-bold text-gray-800">
                                    Админ-панель
                                </Link>
                            </div>

                            <!-- Навигационные ссылки -->
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <Link
                                    :href="route('admin.articles.index')"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                    :class="{ 'border-indigo-500 text-gray-900': route().current('articles.*') }"
                                >
                                    Статьи
                                </Link>
                            </div>
                        </div>

                        <!-- Правая часть навигации -->
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <div class="ml-3 relative">
                                <div class="flex items-center">
                                    <span class="text-gray-700 mr-4">{{ $page.props.auth.user.name }}</span>
                                    <Link
                                        :href="route('profile.edit')"
                                        class="text-gray-500 hover:text-gray-700"
                                    >
                                        Профиль
                                    </Link>
                                    <form @submit.prevent="logout" class="ml-4">
                                        <button
                                            type="submit"
                                            class="text-gray-500 hover:text-gray-700"
                                        >
                                            Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </template>

        <!-- Основной контент -->
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    minimal: {
        type: Boolean,
        default: false
    }
});

const logout = () => {
    router.post(route('logout'));
};
</script> 