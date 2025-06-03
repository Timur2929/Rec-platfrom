<template>
    <AppLayout>
        <Head title="Избранное" />

        <div class="favorites-container">
            <h1 class="page-title">Избранные рецепты</h1>
            
            <div v-if="favorites.length > 0" class="recipes-grid">
                <div v-for="recipe in favorites" :key="recipe.id" class="recipe-card">
                    <div class="card-header">
                        <Link :href="route('recipes.show', recipe.id)" class="recipe-link">
                            <div class="recipe-image">
                                <img 
                                    :src="recipe.image || '/images/recipe-placeholder.jpg'" 
                                    :alt="recipe.title"
                                >
                            </div>
                        </Link>
                        <button @click="removeFromFavorites(recipe.id)" class="favorite-btn">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <Link :href="route('recipes.show', recipe.id)" class="recipe-link">
                            <h3 class="recipe-title">{{ recipe.title }}</h3>
                            <div class="recipe-meta">
                                <span class="meta-item">
                                    <i class="far fa-clock"></i>
                                    {{ recipe.cooking_time }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    {{ recipe.category }}
                                </span>
                            </div>
                            <div class="recipe-rating">
                                <div class="stars">
                                    <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= recipe.rating }">★</span>
                                </div>
                                <span class="reviews-count">{{ recipe.reviews_count }} отзывов</span>
                            </div>
                            <p class="recipe-description">{{ truncateDescription(recipe.description) }}</p>
                        </Link>
                    </div>
                    
                    <div class="card-footer">
                        <div class="recipe-author">
                            <img 
                                :src="recipe.user.profile_photo_url || '/storage/images/profile/default-avatar.png'" 
                                :alt="recipe.user.first_name + ' ' + recipe.user.last_name" 
                                class="author-avatar"
                                @error="$event.target.src = '/storage/images/profile/default-avatar.png'"
                            >
                            <span class="author-name">{{ recipe.user.first_name }} {{ recipe.user.last_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h2>У вас пока нет избранных рецептов</h2>
                <p>Добавляйте понравившиеся рецепты в избранное, чтобы сохранить их для быстрого доступа</p>
                <Link href="/recipes" class="browse-recipes-btn">
                    Посмотреть все рецепты
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    favorites: {
        type: Array,
        default: () => []
    }
});

const removeFromFavorites = (recipeId) => {
    router.post(route('recipes.favorite', recipeId));
};

const truncateDescription = (text) => {
    return text.length > 100 ? text.substring(0, 100) + '...' : text;
};
</script>

<style scoped>
.favorites-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

.page-title {
    font-size: 28px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 30px;
    position: relative;
    display: inline-block;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 3px;
    background: #4CAF50;
}

.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.recipe-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.recipe-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.card-header {
    position: relative;
}

.recipe-image {
    height: 200px;
    overflow: hidden;
}

.recipe-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.recipe-card:hover .recipe-image img {
    transform: scale(1.03);
}

.favorite-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    border: none;
    color: #ff4757;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.favorite-btn:hover {
    background: white;
    color: #ff6b81;
    transform: scale(1.1);
}

.card-body {
    padding: 20px;
    flex-grow: 1;
}

.recipe-link {
    text-decoration: none;
    color: inherit;
}

.recipe-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 12px;
    line-height: 1.3;
}

.recipe-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #718096;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.meta-item i {
    font-size: 14px;
}

.recipe-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    color: #e2e8f0;
    font-size: 16px;
}

.star.filled {
    color: #fbbf24;
}

.reviews-count {
    font-size: 13px;
    color: #718096;
}

.recipe-description {
    color: #4a5568;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 0;
}

.card-footer {
    padding: 15px 20px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

.recipe-author {
    display: flex;
    align-items: center;
    gap: 10px;
}

.author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}

.author-name {
    font-size: 14px;
    color: #4a5568;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    max-width: 500px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 60px;
    color: #e2e8f0;
    margin-bottom: 20px;
}

.empty-state h2 {
    font-size: 22px;
    color: #2d3748;
    margin-bottom: 15px;
    font-weight: 600;
}

.empty-state p {
    color: #718096;
    margin-bottom: 30px;
    line-height: 1.5;
}

.browse-recipes-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: 500;
    font-size: 15px;
}

.browse-recipes-btn:hover {
    background: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 222, 128, 0.2);
}

@media (max-width: 768px) {
    .recipes-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 24px;
    }
}
</style>