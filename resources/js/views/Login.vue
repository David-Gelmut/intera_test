<template>
    <div class="login-page">
        <div class="login-card">
            <h2>Авторизация</h2>
            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="form.email" type="email" placeholder="admin@test.ru"/>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input v-model="form.password" type="password" placeholder="••••••••"/>
                </div>
                <button type="submit">Войти</button>
            </form>
            <div v-if="error" class="error-alert">{{ error }}</div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../store/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const form = ref({ email: '', password: '' });
const error = ref(null);

const handleLogin = async () => {

    error.value = null;

    try {
        await authStore.login(form.value);
        router.push('/settings');
    } catch (err) {
        error.value = err.response?.data?.message;
    }
};
</script>

<style scoped>
.login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f5f5f5;
}

.login-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.form-group {
    margin-bottom: 1.25rem;
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: bold;
    font-size: 0.9rem;
}

input {
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
}

.error {
    color: #e53e3e;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.error-alert {
    background: #fff5f5;
    color: #c53030;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #fed7d7;
    font-size: 0.9rem;
    text-align: center;
}

button {
    width: 100%;
    padding: 0.75rem;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: bold;
}

button:disabled {
    background: #a0aec0;
    cursor: not-allowed;
}
</style>
