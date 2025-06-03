<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { ru } from 'date-fns/locale/ru';

const page = usePage();
const showRejectModal = ref(false);
const showRevisionModal = ref(false);

const form = useForm({
    rejection_reason: ''
});

const revisionForm = useForm({
    revision_comment: ''
});

const props = defineProps({
    recipe: {
        type: Object,
        required: true,
        default: () => ({
            name: '',
            description: '',
            cooking_time: '',
            image: null,
            rating: '0.0',
            views: 0,
            user: {
                id: null,
                name: '',
                profile_photo_url: null
            },
            category: null,
            ingredients: [],
            steps: [],
            reviews: [],
            status: null,
            revision_comment: null,
            rejection_reason: null
        })
    },
    isFavorited: {
        type: Boolean,
        default: false
    }
});

const newReview = ref({
    rating: 0,
    comment: ''
});

const reviewErrors = ref({});
const isSubmitting = ref(false);
const showGallery = ref(false);
const currentGalleryIndex = ref(0);
const isEditing = ref(false);

onMounted(() => {
    console.log('Recipe data:', props.recipe);
    console.log('Recipe status:', props.recipe.status);
    console.log('User role:', page.props.auth.user?.role);
});

const handleImageError = (event) => {
    if (!event.target.dataset.errorHandled) {
        event.target.src = '/images/placeholder.png';
        event.target.dataset.errorHandled = 'true';
    }
};

const toggleFavorite = () => {
    router.post(route('recipes.favorite', props.recipe.id), {}, {
        preserveScroll: true
    });
};

const setRating = (rating) => {
    newReview.value.rating = rating;
};

const submitReview = async () => {
    isSubmitting.value = true;
    reviewErrors.value = {};

    try {
        await router.post(route('recipes.reviews.store', props.recipe.id), newReview.value, {
            preserveScroll: true,
            onSuccess: () => {
                newReview.value = { rating: 0, comment: '' };
            },
            onError: (errors) => {
                reviewErrors.value = errors;
            }
        });
    } finally {
        isSubmitting.value = false;
    }
};

const editReview = (review) => {
    isEditing.value = true;
    newReview.value = {
        rating: review.rating,
        comment: review.comment
    };
};

const updateReview = async (review) => {
    isSubmitting.value = true;
    reviewErrors.value = {};

    try {
        await router.put(route('recipes.reviews.update', [props.recipe.id, review.id]), newReview.value, {
            preserveScroll: true,
            onSuccess: () => {
                isEditing.value = false;
                newReview.value = { rating: 0, comment: '' };
            },
            onError: (errors) => {
                reviewErrors.value = errors;
            }
        });
    } finally {
        isSubmitting.value = false;
    }
};

const cancelEdit = () => {
    isEditing.value = false;
    newReview.value = { rating: 0, comment: '' };
};

const deleteReview = async (review) => {
    if (confirm('Вы уверены, что хотите удалить этот отзыв?')) {
        await router.delete(route('recipes.reviews.destroy', [props.recipe.id, review.id]), {
            preserveScroll: true
        });
    }
};

const formatDate = (date) => {
    return format(new Date(date), 'd MMMM yyyy', { locale: ru });
};

const openGallery = (index) => {
    currentGalleryIndex.value = index;
    showGallery.value = true;
};

const closeGallery = () => {
    showGallery.value = false;
};

const prevImage = () => {
    if (currentGalleryIndex.value > 0) {
        currentGalleryIndex.value--;
    }
};

const nextImage = () => {
    if (currentGalleryIndex.value < props.recipe.gallery.length - 1) {
        currentGalleryIndex.value++;
    }
};

const openStepImage = (step) => {
    // Реализация просмотра изображения шага
    console.log('Open step image:', step);
};

const hasUserReview = computed(() => {
    if (!props.recipe.reviews) return false;
    return props.recipe.reviews.some(review => review.user.id === page.props.auth.user?.id);
});

const userReview = computed(() => {
    if (!props.recipe.reviews) return null;
    return props.recipe.reviews.find(review => review.user.id === page.props.auth.user?.id);
});

const sortedReviews = computed(() => {
    if (!props.recipe.reviews) return [];
    return [...props.recipe.reviews].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const approveRecipe = () => {
    router.post(route('admin.recipes.approve', props.recipe.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('admin.recipes.pending'));
        }
    });
};

const rejectRecipe = () => {
    if (!form.rejection_reason) return;
    
    form.post(route('admin.recipes.reject', props.recipe.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            form.reset();
            router.visit(route('admin.recipes.pending'));
        }
    });
};

const sendToRevision = () => {
    if (!revisionForm.revision_comment) return;
    
    revisionForm.post(route('admin.recipes.revision', props.recipe.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRevisionModal.value = false;
            revisionForm.reset();
            router.visit(route('admin.recipes.pending'));
        }
    });
};
</script>

<template>
    <Head :title="recipe.name" />
    <AppLayout>
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="recipe-header p-6">
                        <div class="recipe-image-container mb-6">
                            <img 
                                :src="recipe.image" 
                                :alt="recipe.name" 
                                class="w-full h-96 object-cover rounded-lg"
                                @error="handleImageError"
                            />
                        </div>

                        <div class="recipe-info">
                            <div class="flex justify-between items-start mb-6">
                                <h1 class="text-3xl font-bold text-gray-900">{{ recipe.name }}</h1>
                                <div v-if="recipe.status === 'approved'" class="flex space-x-4">
                                    <button 
                                        v-if="page.props.auth.user"
                                        @click="toggleFavorite" 
                                        class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition"
                                        :class="[
                                            isFavorited 
                                                ? 'bg-blue-600 text-white border-transparent hover:bg-blue-700'
                                                : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50'
                                        ]"
                                    >
                                        <span class="mr-2">{{ isFavorited ? '❤️' : '🤍' }}</span>
                                        {{ isFavorited ? 'В избранном' : 'В избранное' }}
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center space-x-6 mb-6">
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">⏱</span>
                                    <span class="text-gray-600">{{ recipe.cooking_time }}</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400 mr-2">
                                        <span v-for="n in 5" :key="n" :class="{ 'text-gray-300': n > (recipe.rating || 0) }">★</span>
                                    </div>
                                    <span class="text-gray-600">({{ recipe.rating || '0.0' }})</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">👁</span>
                                    <span class="text-gray-600">{{ recipe.views }} просмотров</span>
                                </div>
                            </div>

                            <div class="flex items-center mb-6">
                                <img 
                                    :src="recipe.user.avatar" 
                                    :alt="recipe.user.full_name"
                                    class="w-10 h-10 rounded-full mr-3"
                                    @error="handleImageError"
                                />
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ recipe.user.full_name }}</div>
                                    <div class="text-sm text-gray-500">Автор рецепта</div>
                                </div>
                            </div>

                            <p class="text-gray-600 mb-6">{{ recipe.description }}</p>

                            <!-- Комментарий администратора для статусов 'revision' и 'rejected' -->
                            <div v-if="recipe.status === 'revision' && recipe.revision_comment" class="mb-6 p-4 rounded-lg bg-yellow-50 border-l-4 border-yellow-400 flex items-start">
                                <span class="mr-3 text-yellow-500 text-2xl">⚠️</span>
                                <div>
                                    <div class="font-semibold text-yellow-700 mb-1">Требуется доработка:</div>
                                    <div class="text-yellow-800">{{ recipe.revision_comment }}</div>
                                </div>
                            </div>
                            <div v-else-if="recipe.status === 'rejected' && recipe.rejection_reason" class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-400 flex items-start">
                                <span class="mr-3 text-red-500 text-2xl">⛔</span>
                                <div>
                                    <div class="font-semibold text-red-700 mb-1">Причина отклонения:</div>
                                    <div class="text-red-800">{{ recipe.rejection_reason }}</div>
                                </div>
                            </div>

                            <div v-if="recipe.category" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ recipe.category.name }}
                            </div>

                            <!-- Кнопки модерации для администратора -->
                            <div v-if="page.props.auth.user && page.props.auth.user.role === 'admin' && recipe.status === 'pending'" class="mt-6 flex gap-3">
                                <button 
                                    @click="approveRecipe"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                                >
                                    Одобрить
                                </button>
                                <button 
                                    @click="showRejectModal = true"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                                >
                                    Отклонить
                                </button>
                                <button
                                    @click="showRevisionModal = true"
                                    class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors"
                                >
                                    На доработку
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Ингредиенты</h2>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <li 
                                    v-for="ingredient in recipe.ingredients" 
                                    :key="ingredient.id"
                                    class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
                                >
                                    <span class="text-gray-900">{{ ingredient.name }}</span>
                                    <span class="text-gray-600">{{ ingredient.amount }} {{ ingredient.unit }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-gray-200">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Способ приготовления</h2>
                            <div class="space-y-6">
                                <div 
                                    v-for="step in recipe.steps" 
                                    :key="step.id" 
                                    class="flex gap-6"
                                >
                                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold">
                                        {{ step.order }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-600">{{ step.description }}</p>
                                        <img 
                                            v-if="step.image"
                                            :src="step.image"
                                            :alt="`Шаг ${step.order}`"
                                            class="mt-4 rounded-lg max-w-md"
                                            @error="handleImageError"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Отзывы -->
                    <div v-if="recipe.status === 'approved'" class="mt-8">
                        <h2 class="text-2xl font-bold mb-4">Отзывы</h2>
                        
                        <!-- Форма отзыва -->
                        <div v-if="!hasUserReview && $page.props.auth.user" class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Оставить отзыв</h3>
                            <form @submit.prevent="submitReview" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Оценка</label>
                                    <div class="flex space-x-2">
                                        <button
                                            v-for="star in 5"
                                            :key="star"
                                            type="button"
                                            @click="newReview.rating = star"
                                            class="text-2xl focus:outline-none"
                                            :class="star <= newReview.rating ? 'text-yellow-400' : 'text-gray-300'"
                                        >
                                            ★
                                        </button>
                                    </div>
                                    <p v-if="reviewErrors.rating" class="mt-1 text-sm text-red-600">{{ reviewErrors.rating }}</p>
                                </div>
                                
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
                                    <textarea
                                        id="comment"
                                        v-model="newReview.comment"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': reviewErrors.comment }"
                                    ></textarea>
                                    <p v-if="reviewErrors.comment" class="mt-1 text-sm text-red-600">{{ reviewErrors.comment }}</p>
                                </div>
                                
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                                    :disabled="isSubmitting"
                                >
                                    {{ isSubmitting ? 'Отправка...' : 'Отправить отзыв' }}
                                </button>
                            </form>
                        </div>

                        <!-- Редактирование отзыва -->
                        <div v-else-if="isEditing && userReview" class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Редактировать отзыв</h3>
                            <form @submit.prevent="updateReview(userReview)" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Оценка</label>
                                    <div class="flex space-x-2">
                                        <button
                                            v-for="star in 5"
                                            :key="star"
                                            type="button"
                                            @click="newReview.rating = star"
                                            class="text-2xl focus:outline-none"
                                            :class="star <= newReview.rating ? 'text-yellow-400' : 'text-gray-300'"
                                        >
                                            ★
                                        </button>
                                    </div>
                                    <p v-if="reviewErrors.rating" class="mt-1 text-sm text-red-600">{{ reviewErrors.rating }}</p>
                                </div>
                                
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
                                    <textarea
                                        id="comment"
                                        v-model="newReview.comment"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': reviewErrors.comment }"
                                    ></textarea>
                                    <p v-if="reviewErrors.comment" class="mt-1 text-sm text-red-600">{{ reviewErrors.comment }}</p>
                                </div>
                                
                                <div class="flex space-x-4">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                                        :disabled="isSubmitting"
                                    >
                                        {{ isSubmitting ? 'Сохранение...' : 'Сохранить' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelEdit"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                    >
                                        Отмена
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Список отзывов -->
                        <div class="space-y-6">
                            <div v-for="review in sortedReviews" :key="review.id" class="bg-white rounded-lg shadow p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold">{{ review.user.full_name }}</span>
                                            <div class="flex">
                                                <span v-for="star in 5" :key="star" class="text-yellow-400">
                                                    {{ star <= review.rating ? '★' : '☆' }}
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-gray-500 text-sm mt-1">{{ formatDate(review.created_at) }}</p>
                                    </div>
                                    <div v-if="review.user.id === $page.props.auth.user?.id" class="flex space-x-2">
                                        <button
                                            @click="editReview(review)"
                                            class="text-blue-500 hover:text-blue-700"
                                            v-if="!isEditing"
                                        >
                                            Редактировать
                                        </button>
                                        <button
                                            @click="deleteReview(review)"
                                            class="text-red-500 hover:text-red-700"
                                            v-if="!isEditing"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-4 text-gray-700">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для галереи -->
        <div v-if="showGallery" class="gallery-modal" @click="closeGallery">
            <div class="gallery-content" @click.stop>
                <button class="close-gallery" @click="closeGallery">×</button>
                <img 
                    :src="`/images/recipes/${recipe.gallery[currentGalleryIndex]}`"
                    :alt="recipe.name"
                    class="gallery-modal-image"
                />
                <button 
                    v-if="currentGalleryIndex > 0" 
                    class="gallery-nav prev" 
                    @click="prevImage"
                >←</button>
                <button 
                    v-if="currentGalleryIndex < recipe.gallery.length - 1" 
                    class="gallery-nav next" 
                    @click="nextImage"
                >→</button>
            </div>
        </div>

        <!-- Модальное окно для отклонения рецепта -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Укажите причину отклонения рецепта
                </h3>
                <div class="mb-4">
                    <textarea
                        v-model="form.rejection_reason"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        rows="4"
                        placeholder="Опишите, почему рецепт отклонен..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showRejectModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Отмена
                    </button>
                    <button
                        @click="rejectRecipe"
                        :disabled="!form.rejection_reason || form.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                    >
                        Отклонить рецепт
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Модальное окно для отправки на доработку -->
        <Modal :show="showRevisionModal" @close="showRevisionModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Укажите замечания для доработки рецепта
                </h3>
                <div class="mb-4">
                    <textarea
                        v-model="revisionForm.revision_comment"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        rows="4"
                        placeholder="Опишите, что нужно исправить..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showRevisionModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Отмена
                    </button>
                    <button
                        @click="sendToRevision"
                        :disabled="!revisionForm.revision_comment || revisionForm.processing"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 disabled:opacity-50"
                    >
                        Отправить на доработку
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<style scoped>
.recipe-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.recipe-header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.recipe-image-container {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    min-height: 400px;
    background-color: #f7fafc;
}

.recipe-main-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
}

.recipe-gallery {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 10px;
}

.gallery-image {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.gallery-image:hover {
    transform: scale(1.05);
}

.recipe-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.recipe-title-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.recipe-actions {
    display: flex;
    gap: 10px;
}

.action-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    color: #4a5568;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.favorite-button.is-favorited {
    background: #4299e1;
    border-color: #4299e1;
    color: white;
}

.edit-button:hover {
    background: #f7fafc;
}

.recipe-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4a5568;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 4px;
}

.star {
    color: #cbd5e0;
    font-size: 20px;
}

.star.filled {
    color: #ecc94b;
}

.rating-value {
    margin-left: 4px;
    color: #4a5568;
}

.author {
    display: flex;
    align-items: center;
    gap: 8px;
}

.author-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.recipe-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #4a5568;
}

.recipe-category {
    display: inline-block;
    padding: 6px 12px;
    background: #e2e8f0;
    border-radius: 6px;
    color: #4a5568;
    font-size: 0.9rem;
}

.recipe-section {
    margin-top: 60px;
}

.recipe-section h2 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 20px;
}

.ingredients-section {
    background: #f7fafc;
    padding: 30px;
    border-radius: 12px;
}

.ingredients-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    list-style: none;
    padding: 0;
}

.ingredient-item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.steps-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.steps-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.step-item {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.step-content {
    flex: 1;
    display: flex;
    gap: 20px;
}

.step-number {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #4299e1;
    color: white;
    border-radius: 50%;
    font-weight: 600;
    flex-shrink: 0;
}

.step-description {
    margin: 0;
    font-size: 1.1rem;
    line-height: 1.6;
    flex: 1;
}

.step-image-container {
    width: 300px;
    height: 200px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
}

.step-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.step-image:hover {
    transform: scale(1.05);
}

.reviews-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.review-form {
    background: #f7fafc;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.rating-input {
    margin-bottom: 15px;
}

.stars {
    display: flex;
    gap: 5px;
}

.star-button {
    background: none;
    border: none;
    font-size: 24px;
    color: #cbd5e0;
    cursor: pointer;
    transition: color 0.2s;
}

.star-button.active {
    color: #ecc94b;
}

.review-input {
    margin-bottom: 15px;
}

.review-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    resize: vertical;
}

.review-textarea.has-error {
    border-color: #e53e3e;
}

.error-message {
    color: #e53e3e;
    font-size: 0.875rem;
    margin-top: 4px;
}

.review-form-actions {
    display: flex;
    gap: 10px;
}

.submit-review-button {
    flex: 1;
    padding: 12px;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.submit-review-button:hover {
    background: #3182ce;
}

.submit-review-button:disabled {
    background: #a0aec0;
    cursor: not-allowed;
}

.cancel-edit-button {
    padding: 12px 24px;
    background: #e2e8f0;
    color: #4a5568;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.cancel-edit-button:hover {
    background: #cbd5e0;
}

.login-prompt {
    text-align: center;
    padding: 20px;
    background: #f7fafc;
    border-radius: 8px;
    margin-bottom: 30px;
}

.login-prompt a {
    color: #4299e1;
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 30px;
}

.review-item {
    background: #f7fafc;
    padding: 20px;
    border-radius: 12px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.review-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.author-name {
    font-weight: 600;
    color: #2d3748;
}

.review-date {
    font-size: 0.875rem;
    color: #718096;
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.review-rating {
    display: flex;
    gap: 2px;
}

.star {
    color: #cbd5e0;
    font-size: 18px;
}

.star.filled {
    color: #ecc94b;
}

.review-text {
    color: #4a5568;
    line-height: 1.6;
    margin: 0 0 15px 0;
    white-space: pre-line;
}

.review-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
}

.review-actions {
    display: flex;
    gap: 10px;
}

.edit-review-button,
.delete-review-button {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 4px;
}

.edit-review-button {
    background: #e2e8f0;
    color: #4a5568;
}

.edit-review-button:hover {
    background: #cbd5e0;
}

.delete-review-button {
    background: #fed7d7;
    color: #e53e3e;
}

.delete-review-button:hover {
    background: #feb2b2;
}

.no-reviews {
    text-align: center;
    padding: 40px;
    background: #f7fafc;
    border-radius: 12px;
    color: #718096;
}

.no-reviews p {
    margin: 0;
    font-size: 1.1rem;
}

.gallery-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.gallery-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}

.gallery-modal-image {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
}

.close-gallery {
    position: absolute;
    top: -40px;
    right: -40px;
    background: none;
    border: none;
    color: white;
    font-size: 40px;
    cursor: pointer;
}

.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    font-size: 24px;
    padding: 20px;
    cursor: pointer;
    transition: background 0.2s;
}

.gallery-nav:hover {
    background: rgba(255, 255, 255, 0.2);
}

.gallery-nav.prev {
    left: -60px;
}

.gallery-nav.next {
    right: -60px;
}

@media (max-width: 768px) {
    .recipe-header {
        grid-template-columns: 1fr;
    }
    
    .recipe-title-section {
        flex-direction: column;
    }
    
    .recipe-actions {
        width: 100%;
    }
    
    .action-button {
        flex: 1;
    }
    
    .step-item {
        flex-direction: column;
    }
    
    .step-image-container {
        width: 100%;
        height: 200px;
    }
    
    .gallery-nav {
        padding: 10px;
    }
    
    .gallery-nav.prev {
        left: 10px;
    }
    
    .gallery-nav.next {
        right: 10px;
    }
}

.user-has-review {
    text-align: center;
    padding: 20px;
    background: #f7fafc;
    border-radius: 8px;
    margin-bottom: 30px;
    color: #4a5568;
}

.user-has-review p {
    margin: 0;
    font-size: 1.1rem;
}

/* Стили для кнопок модерации */
.flex.gap-3 {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.flex-1 {
    flex: 1 1 0%;
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.py-2 {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.rounded-md {
    border-radius: 0.375rem;
}

.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.bg-green-600 {
    background-color: #059669;
}

.hover\:bg-green-700:hover {
    background-color: #047857;
}

.bg-red-600 {
    background-color: #DC2626;
}

.hover\:bg-red-700:hover {
    background-color: #B91C1C;
}

.bg-yellow-500 {
    background-color: #F59E0B;
}

.hover\:bg-yellow-600:hover {
    background-color: #D97706;
}

.text-white {
    color: #ffffff;
}
</style> 