<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Логотип -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')">
                                <img class="block h-9 w-auto" src="/images/logo.svg" alt="Логотип" />
                            </Link>
                        </div>

                        <!-- Навигация -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link :href="route('home')"
                                  class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Главная
                            </Link>
                            <Link :href="route('recipes.index')"
                                  class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Рецепты
                            </Link>
                            <Link :href="route('categories.index')"
                                  class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Категории
                            </Link>
                            <Link :href="route('articles.index')"
                                  class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Статьи
                            </Link>
                        </div>
                    </div>

                    <!-- Кнопки авторизации -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <Link v-if="!$page.props.auth.user" :href="route('login')"
                              class="text-sm text-gray-700 underline">
                            Войти
                        </Link>
                        <Link v-if="!$page.props.auth.user" :href="route('register')"
                              class="ml-4 text-sm text-gray-700 underline">
                            Регистрация
                        </Link>
                        <Link v-else :href="route('profile.show')"
                              class="text-sm text-gray-700 underline">
                            Личный кабинет
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Основной контент -->
        <main>
            <slot />
        </main>

        <!-- Подвал -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    © 2024 Кулинарные рецепты. Все права защищены.
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.min-h-screen {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

main {
    flex: 1;
}
</style> 