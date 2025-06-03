<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
    user: Object
});

const form = useForm({
    email: '',
    firstName: '',
    lastName: '',
    phone: '',
    password: '',
});

const submit = () => {
    form.post(route('register'), {
        onSuccess: () => {
            window.location.href = '/';
        }
    });
};
</script>

<template>
    <div class="page-container">
        <Sidebar :user="user" />
        
        <div class="main-content">
            <div class="register-container">
                <div class="register-card">
                    <div class="register-header">
                        <h2>Создать аккаунт</h2>
                        <p>Заполните форму для регистрации</p>
                    </div>

                    <form @submit.prevent="submit" class="register-form">
                        <div class="input-group">
                            <input 
                                type="email" 
                                id="email"
                                v-model="form.email"
                                class="form-input"
                                placeholder=" "
                                required
                            />
                            <label for="email">E-mail</label>
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </span>
                        </div>

                        <div class="name-fields">
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="firstName"
                                    v-model="form.firstName"
                                    class="form-input"
                                    placeholder=" "
                                    required
                                />
                                <label for="firstName">Имя</label>
                            </div>

                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="lastName"
                                    v-model="form.lastName"
                                    class="form-input"
                                    placeholder=" "
                                    required
                                />
                                <label for="lastName">Фамилия</label>
                            </div>
                        </div>

                        <div class="input-group">
                            <input 
                                type="tel" 
                                id="phone"
                                v-model="form.phone"
                                class="form-input"
                                placeholder=" "
                                required
                            />
                            <label for="phone">Телефон</label>
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </span>
                        </div>

                        <div class="input-group">
                            <input 
                                type="password" 
                                id="password"
                                v-model="form.password"
                                class="form-input"
                                placeholder=" "
                                required
                            />
                            <label for="password">Пароль</label>
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                        </div>

                        <button type="submit" class="register-button" :disabled="form.processing">
                            <span v-if="!form.processing">Зарегистрироваться</span>
                            <span v-else class="loading">Регистрация...</span>
                        </button>

                        <div class="login-link">
                            Уже есть аккаунт? <Link href="/login">Войти</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page-container {
    display: flex;
    min-height: 100vh;
    background-color: #f8fafc;
}

.main-content {
    flex: 1;
    margin-left: 250px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    background-color: #f8fafc;
}

.register-container {
    width: 100%;
    max-width: 480px;
}

.register-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: all 0.3s ease;
}

.register-header {
    padding: 2rem 2rem 1rem;
    text-align: center;
}

.register-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.register-header p {
    color: #64748b;
    font-size: 0.9375rem;
}

.register-form {
    padding: 0 2rem 2rem;
}

.input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-input {
    width: 100%;
    padding: 1rem 1rem 1rem 2.5rem;
    font-size: 0.9375rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background-color: #f8fafc;
    color: #1e293b;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: rgba(28, 34, 46, 1);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background-color: white;
}

.form-input:focus + label,
.form-input:not(:placeholder-shown) + label {
    transform: translateY(-1.25rem) scale(0.85);
    color: rgba(28, 34, 46, 1);
    background-color: white;
    padding: 0 0.25rem;
    left: 1.75rem;
}

.input-group label {
    position: absolute;
    left: 2.5rem;
    top: 1rem;
    color: #64748b;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    pointer-events: none;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 1rem;
    color: #94a3b8;
}

.name-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.register-button {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    background-color: rgba(28, 34, 46, 1);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 0.5rem;
}

.register-button:hover {
    background-color: #4f46e5;
}

.register-button:disabled {
    background-color: #c7d2fe;
    cursor: not-allowed;
}

.loading {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.login-link {
    margin-top: 1.5rem;
    text-align: center;
    color: #64748b;
    font-size: 0.9375rem;
}

.login-link a {
    color: rgba(28, 34, 46, 1);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
}

.login-link a:hover {
    color: #4f46e5;
    text-decoration: underline;
}

/* Адаптивность */
@media (max-width: 1024px) {
    .main-content {
        margin-left: 0;
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .register-card {
        border-radius: 0;
        box-shadow: none;
    }
    
    .register-header {
        padding: 1.5rem 1.5rem 0.5rem;
    }
    
    .register-form {
        padding: 0 1.5rem 1.5rem;
    }
    
    .name-fields {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 1rem;
    }
    
    .register-header h2 {
        font-size: 1.5rem;
    }
    
    .form-input {
        padding: 0.875rem 0.875rem 0.875rem 2.25rem;
    }
    
    .input-icon {
        left: 0.75rem;
        top: 0.875rem;
    }
    
    .input-group label {
        left: 2.25rem;
    }
    
    .form-input:focus + label,
    .form-input:not(:placeholder-shown) + label {
        left: 1.25rem;
    }
}
</style>