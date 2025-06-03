<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    onSuccess: () => {
      window.location.href = '/';
    },
    onError: () => {
      console.error('Ошибка входа');
    }
  })
}
</script>

<template>
  <AppLayout>
    <div class="auth-container">
      <div class="auth-card">
        <div class="auth-header">
          <h2>Добро пожаловать</h2>
          <p>Введите свои данные для входа</p>
        </div>

        <form @submit.prevent="submit" class="auth-form">
          <div class="input-group" :class="{ 'error': form.errors.email }">
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
            <div v-if="form.errors.email" class="error-message">{{ form.errors.email }}</div>
          </div>

          <div class="input-group" :class="{ 'error': form.errors.password }">
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
            <div v-if="form.errors.password" class="error-message">{{ form.errors.password }}</div>
          </div>

          <button type="submit" class="auth-button" :disabled="form.processing">
            <span v-if="!form.processing">Войти</span>
            <span v-else class="loading">
              <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Вход...
            </span>
          </button>

          <div class="auth-footer">
            <span>Нет аккаунта?</span>
            <Link href="/register" class="auth-link">Зарегистрироваться</Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 80px);
  padding: 1rem;
  background-color: #f8fafc;
}

.auth-card {
  width: 100%;
  max-width: 28rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.auth-header {
  padding: 2rem 2rem 1rem;
  text-align: center;
}

.auth-header h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.auth-header p {
  color: #64748b;
  font-size: 0.9375rem;
}

.auth-form {
  padding: 0 2rem 2rem;
}

.input-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.input-group.error .form-input {
  border-color: #dc2626;
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

.error-message {
  color: #dc2626;
  font-size: 0.8125rem;
  margin-top: 0.25rem;
  padding-left: 0.25rem;
}

.auth-button {
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
  display: flex;
  justify-content: center;
  align-items: center;
}

.auth-button:hover:not(:disabled) {
  background-color: #4f46e5;
}

.auth-button:disabled {
  background-color: #c7d2fe;
  cursor: not-allowed;
}

.loading {
  display: inline-flex;
  align-items: center;
}

.auth-footer {
  margin-top: 1.5rem;
  text-align: center;
  color: #64748b;
  font-size: 0.9375rem;
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.auth-link {
  color: rgba(28, 34, 46, 1);
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.auth-link:hover {
  color: #4f46e5;
  text-decoration: underline;
}

/* Адаптивность */
@media (max-width: 768px) {
  .auth-card {
    border-radius: 0;
    box-shadow: none;
  }
  
  .auth-header {
    padding: 1.5rem 1.5rem 0.5rem;
  }
  
  .auth-form {
    padding: 0 1.5rem 1.5rem;
  }
}

@media (max-width: 480px) {
  .auth-header h2 {
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