<template>
  <Head title="Рецепты" />
  
  <AppLayout>
    <div class="recipes-container">
      <header class="recipes-header">
        <h1>Рецепты</h1>
        <Link v-if="$page.props.auth.user" :href="route('recipes.submit')" class="submit-recipe-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button-icon">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Предложить рецепт
        </Link>
      </header>

      <div class="filters-section">
        <div class="search-container">
          <div class="search-input-wrapper">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Поиск рецептов..."
              class="search-input"
              @keyup.enter="filterRecipes"
            >
            <button @click="filterRecipes" class="search-btn">
              <i class="fas fa-search"></i>
            </button>
          </div>
          
          <div class="filter-selectors">
            <div class="filter-selector">
              <label>Тип поиска:</label>
              <select v-model="searchType" class="filter-select">
                <option value="all">Везде</option>
                <option value="title">По названию</option>
                <option value="ingredients">По ингредиентам</option>
              </select>
            </div>
            
            <div class="filter-selector">
              <label>Сортировка:</label>
              <select v-model="sortBy" class="filter-select" @change="filterRecipes">
                <option value="newest">Сначала новые</option>
                <option value="popular">Популярные</option>
                <option value="rating">По рейтингу</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div v-if="recipes.data && recipes.data.length > 0" class="recipes-grid">
        <div v-for="recipe in recipes.data" :key="recipe.id" class="recipe-card">
          <Link :href="route('recipes.show', recipe.id)" class="recipe-image">
            <img :src="recipe.image || '/images/placeholder.png'" :alt="recipe.title" @error="$event.target.src = '/images/placeholder.png'" />
            <div class="recipe-time">{{ recipe.cooking_time }}</div>
          </Link>
          <div class="recipe-content">
            <Link :href="route('recipes.show', recipe.id)" class="recipe-title">
              {{ recipe.title }}
            </Link>
            <div class="recipe-meta">
              <div class="recipe-rating">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span>{{ recipe.rating || '0.0' }}</span>
              </div>
              <div class="recipe-category" v-if="recipe.category">
                {{ recipe.category.name }}
              </div>
              <div class="recipe-views">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>{{ recipe.views }}</span>
              </div>
            </div>
            <p class="recipe-description">{{ recipe.description }}</p>
            <div class="recipe-author" v-if="recipe.user">
              <img :src="recipe.user.avatar || '/images/profile/default-avatar.png'" :alt="recipe.user.full_name" class="author-avatar" />
              <span>{{ recipe.user.full_name }}</span>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="recipes.data && recipes.data.length === 0" class="no-recipes">
        <p>Рецепты не найдены</p>
      </div>
      <div v-else class="loading">
        <p>Загрузка рецептов...</p>
      </div>

      <div v-if="recipes.links && recipes.links.length > 3" class="pagination">
        <Link 
          v-for="link in recipes.links" 
          :key="link.label"
          :href="link.url"
          class="pagination-button"
          :class="{ 'active': link.active }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    recipes: Object,
    filters: Object
});

const searchQuery = ref(props.filters?.search || '');
const searchType = ref(props.filters?.searchType || 'all');
const sortBy = ref(props.filters?.sort || 'newest');

const filterRecipes = () => {
  router.get(route('recipes.index'), {
    search: searchQuery.value,
    searchType: searchType.value,
    sort: sortBy.value
  }, {
    preserveState: true,
    preserveScroll: true
  });
};

watch(searchQuery, (value) => {
    if (value === '') {
        filterRecipes();
    }
});
</script>

<style scoped>
.recipes-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.recipes-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.recipes-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #1a202c;
}

.submit-recipe-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border-radius: 8px;
  font-weight: 600;
  transition: background-color 0.2s;
}

.submit-recipe-button:hover {
  background-color: #45a049;
}

.button-icon {
  width: 20px;
  height: 20px;
}

.filters-section {
  margin-bottom: 30px;
}

.search-container {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.search-input-wrapper {
  display: flex;
  margin-bottom: 15px;
}

.search-input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px 0 0 8px;
  font-size: 15px;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.search-btn {
  padding: 0 18px;
  background: #4CAF50;
  border: none;
  border-radius: 0 8px 8px 0;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s;
}

.search-btn:hover {
  background: #45a049;
}

.filter-selectors {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.filter-selector {
  flex: 1;
  min-width: 200px;
}

.filter-selector label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  color: #4a5568;
  font-weight: 500;
}

.filter-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background-color: white;
  color: #4a5568;
  font-size: 14px;
  cursor: pointer;
  transition: border-color 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #4CAF50;
}

.recipes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 30px;
  margin-top: 30px;
}

.recipe-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.recipe-card:hover {
  transform: translateY(-5px);
}

.recipe-image {
  position: relative;
  display: block;
  height: 200px;
}

.recipe-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.recipe-time {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.recipe-content {
  padding: 20px;
}

.recipe-title {
  display: block;
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 10px;
  text-decoration: none;
}

.recipe-title:hover {
  color: #4299e1;
}

.recipe-meta {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}

.recipe-rating {
  display: flex;
  align-items: center;
  gap: 5px;
  color: #ecc94b;
}

.recipe-category {
  background-color: #e2e8f0;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  color: #4a5568;
}

.recipe-views {
  display: flex;
  align-items: center;
  gap: 5px;
  color: #718096;
}

.recipe-description {
  color: #718096;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.recipe-author {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #718096;
  font-size: 14px;
}

.author-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 5px;
  margin-top: 40px;
}

.pagination-button {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  color: #4a5568;
  text-decoration: none;
  transition: all 0.2s;
}

.pagination-button:hover {
  background-color: #f7fafc;
}

.pagination-button.active {
  background-color: #4299e1;
  color: white;
  border-color: #4299e1;
}

.no-recipes {
  text-align: center;
  padding: 40px;
  color: #718096;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #718096;
}
</style> 