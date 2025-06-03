<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    status: String,
});

const form = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    avatar: undefined,
    cover_image: undefined,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref(props.user.avatar_url || '/images/profile/stas.png');
const coverPreview = ref('/images/profile/KANEKI KEN SAD.png');

watch(() => props.user.avatar, (newVal) => {
    avatarPreview.value = newVal || '/images/profile/stas.png';
});

watch(() => props.user.cover_image, (newVal) => {
    coverPreview.value = newVal || '/images/profile/KANEKI KEN SAD.png';
});

const updateProfile = () => {
    console.log('Form data before submit:', {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
        has_avatar: !!form.avatar,
        has_cover: !!form.cover_image
    });

    form.transform((data) => ({
        ...data,
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
        avatar: form.avatar,
        cover_image: form.cover_image
    })).patch(route('profile.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            console.log('Profile updated successfully');
        },
        onError: (errors) => {
            console.error('Update errors:', errors);
        },
        onFinish: () => {
            console.log('Request finished');
        }
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
    });
};

const updateAvatar = (e) => {
    if (e.target.files[0]) {
        form.avatar = e.target.files[0];
        avatarPreview.value = URL.createObjectURL(e.target.files[0]);
    }
};

const updateCoverImage = (e) => {
    if (e.target.files[0]) {
        form.cover_image = e.target.files[0];
        coverPreview.value = URL.createObjectURL(e.target.files[0]);
    }
};

const submit = () => {
    // Важно: используем FormData для отправки файлов
    const formData = new FormData();
    
    // Добавляем все поля формы
    Object.keys(form.data()).forEach(key => {
        // Для файлов добавляем как есть, для остальных - как строки
        if (key === 'avatar' || key === 'cover_image') {
            if (form[key]) {
                formData.append(key, form[key]);
            }
        } else {
            formData.append(key, form[key]);
        }
    });

    // Отправляем FormData вместо обычного объекта
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Обработка успешного сохранения
        },
        onError: (errors) => {
            // Обработка ошибок
        },
        forceFormData: true, // Важно: принудительно используем FormData
    });
};
</script>

<template>
    <Head title="Редактирование профиля" />

    <div class="min-h-screen bg-white">
        <Sidebar :user="user" />

        <div class="main-content">
            <div class="profile-edit-container">
                <div class="profile-edit-header">
                    <h1>Редактирование профиля</h1>
                    <p v-if="status" class="status-message">{{ status }}</p>
                </div>

                <form @submit.prevent="updateProfile" class="profile-edit-form" enctype="multipart/form-data">
                    <div class="images-section">
                        <div class="image-upload">
                            <h3>Аватар профиля</h3>
                            <div class="image-preview">
                                <img :src="avatarPreview" alt="Аватар" />
                                <div class="image-upload-overlay">
                                    <input type="file" @change="updateAvatar" accept="image/*" id="avatar-input" class="hidden-input" />
                                    <label for="avatar-input" class="upload-button">Изменить фото</label>
                                </div>
                            </div>
                        </div>

                        <div class="image-upload">
                            <h3>Фоновое изображение</h3>
                            <div class="image-preview cover-preview">
                                <img :src="coverPreview" alt="Фон" />
                                <div class="image-upload-overlay">
                                    <input type="file" @change="updateCoverImage" accept="image/*" id="cover-input" class="hidden-input" />
                                    <label for="cover-input" class="upload-button">Изменить фото</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label for="first_name">Имя</label>
                            <input 
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                class="form-input"
                                required
                            />
                            <span v-if="form.errors.first_name" class="error-message">{{ form.errors.first_name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input 
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                class="form-input"
                                required
                            />
                            <span v-if="form.errors.last_name" class="error-message">{{ form.errors.last_name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input 
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-input"
                                required
                            />
                            <span v-if="form.errors.email" class="error-message">{{ form.errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input 
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                class="form-input"
                            />
                            <span v-if="form.errors.phone" class="error-message">{{ form.errors.phone }}</span>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-button" :disabled="form.processing">
                                {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
                            </button>
                        </div>
                    </div>
                </form>

                <form @submit.prevent="updatePassword" class="profile-edit-form mt-6">
                    <div class="form-section">
                        <h2 class="section-title">Изменить пароль</h2>
                        
                        <div class="form-group">
                            <label for="current_password">Текущий пароль</label>
                            <input 
                                id="current_password"
                                v-model="passwordForm.current_password"
                                type="password"
                                class="form-input"
                                required
                            />
                            <span v-if="passwordForm.errors.current_password" class="error-message">
                                {{ passwordForm.errors.current_password }}
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <input 
                                id="password"
                                v-model="passwordForm.password"
                                type="password"
                                class="form-input"
                                required
                            />
                            <span v-if="passwordForm.errors.password" class="error-message">
                                {{ passwordForm.errors.password }}
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <input 
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="form-input"
                                required
                            />
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-button" :disabled="passwordForm.processing">
                                {{ passwordForm.processing ? 'Сохранение...' : 'Изменить пароль' }}
                            </button>
                            <p v-if="passwordForm.recentlySuccessful" class="success-message">
                                Пароль успешно изменен
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.main-content {
    margin-left: 250px;
    min-height: 100vh;
    background-color: #f7fafc;
}

.profile-edit-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.profile-edit-header {
    margin-bottom: 40px;
    text-align: center;
}

.profile-edit-header h1 {
    font-size: 32px;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 8px;
}

.status-message {
    color: #48bb78;
    font-size: 16px;
}

.profile-edit-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.images-section {
    padding: 32px;
    background: #f7fafc;
    border-bottom: 1px solid #e2e8f0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 32px;
}

.image-upload h3 {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 16px;
    color: #2d3748;
}

.image-preview {
    position: relative;
    width: 100%;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
}

.cover-preview {
    height: 150px;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-upload-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.image-preview:hover .image-upload-overlay {
    opacity: 1;
}

.hidden-input {
    display: none;
}

.upload-button {
    padding: 8px 16px;
    background: white;
    color: #2d3748;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.upload-button:hover {
    background: #edf2f7;
}

.form-section {
    padding: 32px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 16px;
    color: #2d3748;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #4a5568;
}

.error-message {
    color: #e53e3e;
    font-size: 14px;
    margin-top: 4px;
}

.form-actions {
    margin-top: 32px;
    text-align: right;
}

.save-button {
    padding: 12px 24px;
    background: #4a5568;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.save-button:hover:not(:disabled) {
    background: #2d3748;
}

.save-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 24px;
}

.mt-6 {
    margin-top: 24px;
}

.success-message {
    color: #48bb78;
    font-size: 14px;
    margin-left: 12px;
    display: inline-block;
}
</style>
