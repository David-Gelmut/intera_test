<template>
  <div
      class="min-h-screen flex flex-col justify-center items-center bg-slate-50 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased">
    <div class="w-full max-w-md space-y-8">


      <div class="bg-white rounded-xl shadow-md border border-slate-200/80 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-6">Восстановление пароля</h3>

        <form @submit.prevent="sendResetLink" class="space-y-5">

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                </svg>
              </span>
              <input
                  v-model="email"
                  type="email"
                  placeholder="admin@test.ru"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
              />
            </div>
          </div>

          <button type="submit" :disabled="loading"
                  class="w-full mt-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex justify-center items-center gap-2 cursor-pointer font-semibold">
            Отправить ссылку
          </button>

        </form>

        <div class="mt-5 text-center text-sm">
          <span class="text-slate-500">Войти в аккаунт со старым паролем</span>
          <router-link to="/login" class="ml-1 font-semibold text-blue-600 hover:text-blue-500 transition-colors">
            Войти
          </router-link>
        </div>

        <div v-if="error"
             class="mt-4 p-4 bg-rose-50 border border-rose-100 text-sm text-rose-700 rounded-lg flex gap-2 items-start animate-fade-in">
          <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ error }}</span>
        </div>

        <div v-if="message"
             class="mt-4 p-4 bg-green-50 border border-rose-100 text-sm text-green-700 rounded-lg flex gap-2 items-start animate-fade-in">
          <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ message }}</span>
        </div>

      </div>

    </div>
  </div>
</template>
<script setup>
import {ref} from 'vue';
import axios from 'axios';

const email = ref('');
const message = ref('');
const error = ref('');
const loading = ref(false);

const sendResetLink = async () => {
  loading.value = true;
  message.value = '';
  error.value = '';

  try {

    await axios.get('/sanctum/csrf-cookie');

    // Отправляем запрос на маршрут Fortify (с вашим префиксом /api)
    const response = await axios.post('/api/forgot-password', {email: email.value});

    // Fortify возвращает JSON с текстом успешной отправки
    message.value = response.data.status || 'Ссылка для сброса отправлена на ваш Email!';
  } catch (err) {
    error.value = err.response?.data?.message || 'Произошла ошибка. Проверьте Email.';
  } finally {
    loading.value = false;
  }
};
</script>
<style scoped>

</style>