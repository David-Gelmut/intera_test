<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-900 px-4 font-sans">
    <div class="w-full max-w-md text-center bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">

      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-500/10 text-rose-500 animate-pulse">
        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>

      <h2 class="mt-6 text-2xl font-bold tracking-tight text-white">Доступ ограничен</h2>

      <p class="mt-4 text-sm text-slate-400 leading-relaxed">
        Ваш аккаунт был заблокирован администрацией системы. Если вы считаете, что это ошибка, пожалуйста, обратитесь в службу поддержки.
      </p>

      <div class="mt-8">

        <button
            @click="handleLogout"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-rose-500 active:scale-[0.98] cursor-pointer focus:outline-none"
        >
          <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Войти в другой аккаунт
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const handleLogout = async () => {
  try {
    await axios.post('/api/logout');
  } catch (error) {
    console.error('Ошибка при выходе на сервере:', error);
  } finally {
    localStorage.removeItem('user');
    window.location.href = '/login';
  }
};
</script>
