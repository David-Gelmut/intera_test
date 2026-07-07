<template>
  <div class="min-h-screen flex flex-col justify-center items-center bg-slate-50 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased">
    <div class="w-full max-w-md space-y-8">

      <!-- Логотип и приветствие над карточкой -->
      <div class="text-center">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm mb-4">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Intera Maps</h2>
        <p class="mt-2 text-sm text-slate-500">Создайте аккаунт для начала работы с платформой</p>
      </div>

      <!-- Карточка регистрации -->
      <div class="bg-white rounded-xl shadow-md border border-slate-200/80 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-6">Регистрация</h3>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Поле Имя пользователя -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Имя</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </span>
              <input
                  v-model="form.name"
                  type="text"
                  placeholder="Иван Иванов"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
              />
            </div>
          </div>

          <!-- Поле Email -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                </svg>
              </span>
              <input
                  v-model="form.email"
                  type="email"
                  placeholder="admin@test.ru"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
              />
            </div>
          </div>

          <!-- Поле Пароль -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Пароль</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </span>
              <input
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
              />
            </div>
          </div>

          <!-- Поле Подтверждение пароля -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Подтверждение пароля</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </span>
              <input
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="••••••••"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs"
                  :class="{ 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500': isPasswordMismatch }"
              />
            </div>
            <!-- Быстрая фронтенд-подсказка, если пароли не совпадают -->
<!--            <p v-if="isPasswordMismatch" class="text-xs text-rose-600 font-medium">Пароли в данный момент не совпадают.</p>-->
          </div>

          <!-- Кнопка отправки -->
          <button
              type="submit"
              :disabled="isLoading"
              class="w-full mt-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:ring-offset-2 inline-flex justify-center items-center gap-2 cursor-pointer disabled:bg-blue-400 disabled:cursor-not-allowed"
          >
            <svg v-if="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? 'Регистрация...' : 'Создать аккаунт' }}
          </button>
        </form>

        <!-- Переход на страницу логина -->
        <div class="mt-5 text-center text-sm">
          <span class="text-slate-500">Уже есть аккаунт?</span>
          <router-link to="/login" class="ml-1 font-semibold text-blue-600 hover:text-blue-500 transition-colors">
            Войти
          </router-link>
        </div>

        <!-- Блок ошибки -->
        <div v-if="error" class="mt-4 p-4 bg-rose-50 border border-rose-100 text-sm text-rose-700 rounded-lg flex gap-2 items-start">
          <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ error }}</span>
        </div>
      </div>

    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../store/auth.js';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const isLoading = ref(false);
const form = ref({ email: '', password: '', name: '', password_confirmation: ''});
const error = ref(null);

const handleRegister = async () => {

  error.value = null;

  try {
    await authStore.register(form.value);
    router.push('/convert');
  } catch (err) {
    console.log(err);
    error.value = err.response?.data?.message;
  }
};

</script>
