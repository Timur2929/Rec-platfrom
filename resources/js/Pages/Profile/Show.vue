<template>
  <Head title="Профиль" />
  
  <div class="min-h-screen bg-white">
    <Sidebar :user="user" />
    
    <div class="main-content">
      <div class="profile-header">
        <!-- Фоновое изображение профиля -->
        <div class="cover-photo">
          <img 
            :src="user.cover_image" 
            alt="Фоновое изображение"
            class="w-full h-full object-cover"
          />
        </div>
        
        <!-- Profile Info -->
        <div class="profile-info">
          <div class="avatar-container">
            <img 
              :src="user.avatar_url" 
              :alt="user.first_name + ' ' + user.last_name"
              class="avatar"
            />
          </div>
          <h1 class="name">{{ user.first_name }} {{ user.last_name }}</h1>
          <div class="contact-info">
            <span class="phone">{{ user.phone }}</span>
            <span class="separator">–</span>
            <span class="email">{{ user.email }}</span>
          </div>
          <button class="edit-button" @click="$inertia.visit(route('profile.edit'))">
            Изменить
          </button>
        </div>
      </div>

      <div class="recipes-section">
        <!-- Секция для администратора -->
        <div v-if="user.role === 'admin'" class="admin-section">
          <h2 class="section-title">
            Заявки на рассмотрении 
          </h2>
          
          <div v-if="pendingRecipes && pendingRecipes.length > 0" class="recipes-grid">
            <div v-for="recipe in pendingRecipes" :key="recipe.id" class="recipe-card">
              <div class="recipe-image-container">
                <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
                <div class="recipe-time">{{ recipe.cooking_time }}</div>
              </div>
              <div class="recipe-content">
                <h3 class="recipe-title">{{ recipe.title }}</h3>
                <div class="recipe-info">
                  <div class="info-row">
                    <span class="info-label">Категория:</span>
                    <span class="info-value">{{ recipe.category?.name }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Время приготовления:</span>
                    <span class="info-value">{{ recipe.cooking_time }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Автор:</span>
                    <span class="info-value">{{ recipe.author }}</span>
                  </div>
                </div>
                <div class="flex gap-2">
                  <Link :href="route('recipes.show', recipe.id)" class="view-recipe-button">
                    Перейти к рецепту
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="no-pending-recipes">
            <p>Заявок нет</p>
          </div>
        </div>

        <!-- Существующая секция с рецептами -->
        <h2 class="recipes-title">
          Ваши рецепты
          
        </h2>
        
        <div v-if="user.recipes && user.recipes.length > 0" class="recipes-grid">
          <div v-for="recipe in user.recipes" :key="recipe.id" class="recipe-card profile-recipe-card">
            <Link :href="route('recipes.show', recipe.id)" class="recipe-link">
              <div class="recipe-image-container profile-image-container">
                <img :src="recipe.image" :alt="recipe.title" class="recipe-image profile-image" />
                <span v-if="recipe.status === 'pending'" class="status-badge profile-status-badge bg-yellow-100 text-yellow-800">На рассмотрении</span>
                <span v-else-if="recipe.status === 'approved'" class="status-badge profile-status-badge bg-green-100 text-green-800">Одобрен</span>
                <span v-else-if="recipe.status === 'rejected'" class="status-badge profile-status-badge bg-red-100 text-red-800">Отклонён</span>
                <span v-else-if="recipe.status === 'revision'" class="status-badge profile-status-badge bg-yellow-200 text-yellow-900">На доработке</span>
                <span class="profile-cooking-time">{{ recipe.cooking_time }}</span>
              </div>
              <div class="recipe-content profile-recipe-content">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-xs text-gray-500">Автор:</span>
                  <span class="text-xs text-gray-700 font-semibold">{{ user.first_name }} {{ user.last_name }}</span>
                </div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                  <span class="recipe-category text-xs bg-gray-100 px-2 py-1 rounded" v-if="recipe.category">{{ recipe.category }}</span>
                </div>
                <h3 class="recipe-title font-semibold text-base mb-1 line-clamp-1">{{ recipe.title }}</h3>
                <p class="recipe-description text-xs text-gray-600 mb-2 line-clamp-2">{{ recipe.description }}</p>
                <div class="flex items-center gap-1 mb-1">
                  <span v-for="i in 5" :key="i" class="star profile-star" :class="{ 'filled': i <= (recipe.rating || 0) }">★</span>
                  <span class="text-xs text-gray-500 ml-1">{{ recipe.rating || '0.0' }}</span>
                </div>
              </div>
            </Link>
            <div v-if="recipe.status === 'revision'" class="mt-2 text-center">
              <Link :href="route('recipes.edit', recipe.id)" class="edit-recipe-button">
                Редактировать
              </Link>
            </div>
          </div>
        </div>
        <div v-else class="no-recipes">
          <p class="text-gray-500 mb-4">У вас пока нет рецептов</p>
          <button @click="navigateToSubmitRecipe" class="suggest-recipe-button">
            Предложить рецепт
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    pendingRecipes: {
        type: Array,
        default: () => []
    }
});

const navigateToSubmitRecipe = () => {
    router.visit('/recipes/submit');
};

const approveRecipe = (recipeId) => {
    router.post(route('admin.recipes.approve', recipeId));
};

const rejectRecipe = (recipeId) => {
    router.post(route('admin.recipes.reject', recipeId));
};
</script>

<style scoped>
.main-content {
    margin-left: 250px;
    min-height: 100vh;
    background-color: #fff;
}

.profile-header {
    position: relative;
    height: 500px;
}

.cover-photo {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 520px;
    overflow: hidden;
}

.cover-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 50px;
    text-align: center;
}

.avatar-container {
    width: 150px;
    height: 150px;
    margin: 0 auto 20px;
    border: 4px solid white;
    border-radius: 50%;
    overflow: hidden;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.name {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #ffffff;
}

.contact-info {
    font-size: 16px;
    margin-bottom: 20px;
    color: #ffffff;
}

.separator {
    margin: 0 10px;
    color: #ffffff;
}

.edit-button {
    background-color: #666;
    color: white;
    padding: 8px 24px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.edit-button:hover {
    background-color: #555;
}

.recipes-section {
    padding: 40px;
}

.recipes-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.arrow {
    color: #666;
}

.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.recipe-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.recipe-card:hover {
    transform: translateY(-5px);
}

.recipe-image-container {
    position: relative;
    width: 100%;
    height: 200px;
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

.recipe-time {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 14px;
}

.recipe-content {
    padding: 20px;
}

.recipe-info {
    margin: 16px 0;
}

.info-row {
    display: flex;
    margin-bottom: 8px;
    font-size: 14px;
    color: #4a5568;
}

.info-label {
    font-weight: 500;
    color: #2d3748;
    width: 140px;
}

.info-value {
    color: #4a5568;
}

.recipe-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 12px;
}

.recipe-category {
    color: #4a5568;
    font-size: 14px;
    margin-bottom: 8px;
}

.recipe-description {
    color: #4a5568;
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.4;
}

.recipe-author {
    font-size: 14px;
    color: #4a5568;
    margin-bottom: 16px;
}

.recipe-author span {
    color: #2d3748;
    font-weight: 500;
}

.view-recipe-button {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #4a5568;
    color: white;
    text-align: center;
    border-radius: 6px;
    font-weight: 500;
    transition: background-color 0.2s;
    text-decoration: none;
    margin-top: 20px;
}

.view-recipe-button:hover {
    background-color: #2d3748;
}

.no-recipes {
    text-align: center;
    padding: 40px;
}

.suggest-recipe-button {
    background-color: #4CAF50;
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s;
}

.suggest-recipe-button:hover {
    background-color: #45a049;
}

.admin-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1a202c;
}

.no-pending-recipes {
    text-align: center;
    padding: 40px;
    background-color: #f7fafc;
    border-radius: 12px;
    color: #4a5568;
}

.recipe-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
}

.action-button {
    flex: 1;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}

.action-button.approve {
    background-color: #10B981;
    color: white;
}

.action-button.approve:hover {
    background-color: #059669;
}

.action-button.reject {
    background-color: #EF4444;
    color: white;
}

.action-button.reject:hover {
    background-color: #DC2626;
}

.action-button.review {
    background-color: #F59E0B;
    color: white;
}

.action-button.review:hover {
    background-color: #D97706;
}

.status-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  z-index: 2;
}
.star {
  color: #cbd5e0;
  font-size: 16px;
}
.star.filled {
  color: #ecc94b;
}

.profile-recipe-card {
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
  transition: box-shadow 0.2s, transform 0.2s;
  background: #fff;
  border: 1px solid #f3f4f6;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 420px;
}
.profile-recipe-card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,0.13), 0 2px 8px rgba(0,0,0,0.07);
  transform: translateY(-4px) scale(1.02);
}
.profile-image-container {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  border-radius: 18px 18px 0 0;
}
.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}
.profile-recipe-card:hover .profile-image {
  transform: scale(1.04);
}
.profile-status-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  z-index: 2;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.profile-recipe-content {
  padding: 18px;
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}
.profile-star {
  color: #e2e8f0;
  font-size: 16px;
  transition: color 0.2s;
}
.profile-star.filled {
  color: #fbbf24;
}
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.profile-cooking-time {
  position: absolute;
  right: 14px;
  bottom: 14px;
  background: rgba(44, 44, 44, 0.85);
  color: #fff;
  padding: 5px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  z-index: 2;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
</style> 