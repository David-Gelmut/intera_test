<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 sm:px-6 lg:px-8 font-sans">
    <div class="w-full max-w-md space-y-8 rounded-2xl bg-white p-8 shadow-xl shadow-slate-100 ring-1 ring-slate-100">

      <!-- Иконка и Заголовок -->
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 animate-bounce">
          <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5" />
          </svg>
        </div>
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">
          Подтвердите ваш Email
        </h2>
      </div>

      <!-- Текстовое описание -->
      <div class="space-y-3 text-center text-sm text-slate-600">
        <p>
          Мы отправили ссылку для подтверждения на вашу почту. Пожалуйста, проверьте ящик.
        </p>
        <div class="rounded-lg bg-amber-50 p-3 text-amber-800 border border-amber-100 text-xs font-medium">
          Пока вы не подтвердите почту, доступ к панели управления закрыт.
        </div>
      </div>

      <!-- Блок уведомлений об успехе -->
      <transition
          enter-active-class="transform ease-out duration-300 transition"
          enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
          enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      >
        <div v-if="message" class="rounded-lg bg-emerald-50 p-3 text-emerald-800 border border-emerald-100 text-sm text-center font-medium">
          {{ message }}
        </div>
      </transition>

      <!-- Действия (Кнопки) -->
      <div class="space-y-3 pt-2">
        <!-- Кнопка повторной отправки -->
        <button
            @click="resendEmail"
            :disabled="loading"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-500 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 cursor-pointer"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Отправка...' : 'Отправить письмо еще раз' }}
        </button>

        <!-- Кнопка Выхода -->
        <button
            @click="handleLogout"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/20"
        >
          <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Выйти из аккаунта
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import {useAuthStore} from "../store/auth.js";

const authStore = useAuthStore();
const router = useRouter();
const loading = ref(false);
const message = ref('');

// Повторная отправка письма через Fortify (маршрут с префиксом /api)
const resendEmail = async () => {
  loading.value = true;
  message.value = '';
  try {
    await axios.get('/sanctum/csrf-cookie');
    await axios.post('/api/email/verification-notification');
    message.value = 'Новая ссылка для подтверждения успешно отправлена на ваш Email!';
  } catch (error) {
    message.value = 'Не удалось отправить письмо. Возможно, сессия истекла. Попробуйте перезайти в аккаунт.';
  } finally {
    loading.value = false;
  }
};
// Кнопка выхода, если пользователь хочет зайти под другим аккаунтом
const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
