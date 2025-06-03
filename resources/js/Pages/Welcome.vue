<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    popularCategories: {
        type: Array,
        required: true
    },
    tastyRecipes: {
        type: Array,
        required: true
    },
    articles: {
        type: Array,
        required: true
    }
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Кулинарные рецепты" />
    <AppLayout>
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Мир Кулинарных Рецептов</h1>
                <p class="hero-subtitle">Откройте для себя удивительные рецепты от шеф-поваров со всего мира</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ tastyRecipes.length }}+</span>
                        <span class="stat-label">Рецептов</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ popularCategories.length }}+</span>
                        <span class="stat-label">Категорий</span>
                    </div>
                </div>
            </div>
            <div class="hero-decoration">
                <div class="floating-element element-1"></div>
                <div class="floating-element element-2"></div>
                <div class="floating-element element-3"></div>
            </div>
        </div>

        <!-- Popular Categories Section -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    Популярные Категории
                </h2>
                <p class="section-subtitle">Выберите категорию блюд по вашему вкусу</p>
            </div>
            <div class="categories-grid">
                <Link 
                    v-for="category in popularCategories" 
                    :key="category.id"
                    :href="route('categories.show', category.id)"
                    class="category-card"
                >
                    <div class="category-image-wrapper">
                        <img :src="category.image" :alt="category.name" class="category-image" />
                        <div class="category-overlay">
                            <div class="category-content">
                                <h3 class="category-name">{{ category.name }}</h3>
                                <span class="category-arrow">→</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Tasty Recipes Section -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    Лучшие Рецепты
                </h2>
                <p class="section-subtitle">Самые популярные и вкусные рецепты от наших поваров</p>
            </div>
            <div class="recipes-grid">
                <Link 
                    v-for="recipe in tastyRecipes" 
                    :key="recipe.id"
                    :href="route('recipes.show', recipe.id)"
                    class="recipe-card"
                >
                    <div class="recipe-image-wrapper">
                        <img :src="recipe.image" :alt="recipe.name" class="recipe-image" />
                        <div class="recipe-badge">
                            <div class="rating-stars">
                                <span v-for="i in 5" :key="i" class="star" :class="{ 'star-filled': i <= recipe.rating }">★</span>
                            </div>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <div class="recipe-category-tag">{{ recipe.category.name }}</div>
                        <h3 class="recipe-title">{{ recipe.title }}</h3>
                        <div class="recipe-meta">
                            <div class="recipe-time">
                                <svg class="time-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                                {{ recipe.cooking_time }}
                            </div>
                            <div class="recipe-views">{{ recipe.views }} просмотров</div>
                        </div>
                        <div class="recipe-author">
                            <img 
                                :src="recipe.user.profile_photo_url || '/storage/images/logo.png'"
                                :alt="recipe.user.first_name + ' ' + recipe.user.last_name"
                                class="author-avatar"
                                @error="$event.target.src = '/storage/images/logo.png'"
                            />
                            <div class="author-info">
                                <span class="author-name">{{ recipe.user.first_name }} {{ recipe.user.last_name }}</span>
                                <div class="recipe-rating">
                                    <span class="rating-value">{{ Number(recipe.rating || 0).toFixed(1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
/* Hero Section */
.hero-section {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 4rem 0;
    margin: -2.5rem -2.5rem 4rem -2.5rem;
    overflow: hidden;
    color: white;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 0.8s ease;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    animation: fadeInUp 0.8s ease 0.2s both;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    animation: fadeInUp 0.8s ease 0.4s both;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.hero-decoration {
    position: absolute;
    inset: 0;
    z-index: 1;
}

.floating-element {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.element-1 {
    width: 100px;
    height: 100px;
    background: #fff;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.element-2 {
    width: 150px;
    height: 150px;
    background: #fff;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.element-3 {
    width: 80px;
    height: 80px;
    background: #fff;
    bottom: 20%;
    left: 80%;
    animation-delay: 4s;
}

/* Content Sections */
.content-section {
    margin-bottom: 4rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.title-icon {
    font-size: 2rem;
}

.section-subtitle {
    font-size: 1.125rem;
    color: #718096;
    max-width: 600px;
    margin: 0 auto;
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.category-card {
    position: relative;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 200px;
}

.category-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.category-image-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(45deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.category-card:hover .category-overlay {
    background: linear-gradient(45deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2));
}

.category-content {
    text-align: center;
    color: white;
}

.category-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-arrow {
    font-size: 1.25rem;
    transition: transform 0.3s ease;
}

.category-card:hover .category-arrow {
    transform: translateX(5px);
}

/* Recipes Grid */
.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
}

.recipe-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    cursor: pointer;
}

.recipe-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.recipe-image-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.recipe-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.recipe-card:hover .recipe-image {
    transform: scale(1.05);
}

.recipe-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 0.5rem;
    border-radius: 0.5rem;
}

.rating-stars {
    display: flex;
    gap: 0.125rem;
}

.star {
    color: #e2e8f0;
    font-size: 0.875rem;
}

.star-filled {
    color: #fbbf24;
}

.recipe-content {
    padding: 1.5rem;
}

.recipe-category-tag {
    display: inline-block;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.recipe-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.recipe-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #718096;
}

.recipe-time {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.time-icon {
    width: 1rem;
    height: 1rem;
    stroke-width: 2;
}

.recipe-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
}

.author-info {
    flex: 1;
}

.author-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.25rem;
}

.recipe-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.rating-value {
    font-size: 0.75rem;
    color: #718096;
}

/* Articles Grid */
.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.article-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    cursor: pointer;
}

.article-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.article-image-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.article-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.article-card:hover .article-image {
    transform: scale(1.05);
}

.article-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50%;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.1));
}

.article-content {
    padding: 1.5rem;
}

.article-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-excerpt {
    color: #718096;
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.75rem;
    color: #a0aec0;
}

.article-date,
.article-views {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.meta-icon {
    width: 0.875rem;
    height: 0.875rem;
    stroke-width: 2;
}

.read-more-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #667eea;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.article-card:hover .read-more-btn {
    color: #764ba2;
    gap: 0.75rem;
}

.arrow-icon {
    width: 1rem;
    height: 1rem;
    stroke-width: 2;
    transition: transform 0.3s ease;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        gap: 1.5rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .categories-grid,
    .recipes-grid,
    .articles-grid {
        grid-template-columns: 1fr;
    }
}
</style>