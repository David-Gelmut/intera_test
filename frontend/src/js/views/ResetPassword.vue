<template>
  <div
      class="min-h-screen flex flex-col justify-center items-center bg-slate-50 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased">
    <div class="w-full max-w-md space-y-8">

      <div class="bg-white rounded-xl shadow-md border border-slate-200/80 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-6">Придумайте новый пароль</h3>

        <form @submit.prevent="submitNewPassword" class="space-y-5">

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Новый пароль</label>
            <div class="relative">
              <input
                  v-model="password"
                  type="password"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
              />
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Подтвердите пароль</label>
            <div class="relative">
              <input
                  v-model="password_confirmation"
                  type="password"
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
          <span class="text-slate-500">Войти в аккаунт</span>
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
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const password = ref('');
const password_confirmation = ref('');
const token = ref('');
const email = ref('');
const message = ref('');
const error = ref('');
//const loading = ref(false);

onMounted(() => {
  // Достаем токен из параметров пути (/reset-password/:token)
  token.value = route.params.token;
  // Достаем email из query-строки (?email=...)
  email.value = route.query.email;
});

const submitNewPassword = async () => {
  //loading.value = true;
  message.value = '';
  error.value = '';

  try {
    await axios.get('/sanctum/csrf-cookie');

    // Отправляем все необходимые данные в Fortify
    const response = await axios.post('/api/reset-password', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    });

    message.value = response.data.status || 'Пароль успешно изменен! Перенаправляем на вход...';

    // Через 3 секунды отправляем на страницу логина
    setTimeout(() => {
      router.push('/login');
    }, 3000);

  } catch (err) {
    error.value = err.response?.data?.message || 'Не удалось изменить пароль. Возможно, ссылка устарела.';
  } finally {
   // loading.value = false;
  }
};
</script>
<style scoped>

</style>