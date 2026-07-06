<template>
  <div class="space-y-6">
    <!-- Заголовок страницы -->
    <div class="border-b border-slate-200 pb-5">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Профиль пользователя</h1>
      <p class="mt-1 text-sm text-slate-500">Управляйте своими личными данными и настройками безопасности.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

      <!-- Левая колонка: Карточка визитки -->
      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 text-center">
        <div
            class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-4 font-bold text-2xl uppercase tracking-wider">
          {{ userInitials }}
        </div>
        <h2 class="text-xl font-bold text-slate-900">{{ authStore.user?.name || 'Пользователь' }}</h2>
        <p class="text-sm text-slate-500 mt-0.5">{{ authStore.user?.email }}</p>

        <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3">
          <div class="flex justify-between text-xs">
            <span class="text-slate-400 font-medium">Статус аккаунта</span>
            <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Активен</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-slate-400 font-medium">Роль системы</span>
            <span class="text-slate-700 font-semibold">Администратор</span>
          </div>
        </div>
      </div>

      <!-- Правая колонка: Формы редактирования -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Блок 1: Основные данные -->
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
          <h3 class="text-lg font-bold text-slate-900 mb-1">Личная информация</h3>
          <p class="text-sm text-slate-500 mb-6">Обновите имя профиля и контактный адрес электронной почты.</p>

          <form @submit.prevent="handleUpdateProfile" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="flex flex-col gap-1.5">
                <label for="profile-name" class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ваше
                  имя</label>
                <input
                    id="profile-name"
                    v-model="profileForm.name"
                    type="text"
                    required
                    class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-900 text-sm shadow-xs focus:outline-none focus:ring-2 transition-all"
                    :class="errors.name ? 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500' : 'border-slate-300 focus:ring-blue-500/20 focus:border-blue-500'"
                />
                <p v-if="errors.name" class="text-xs text-rose-600 font-medium">{{ errors.name }}</p>
              </div>

              <div class="flex flex-col gap-1.5">
                <label for="profile-email"
                       class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
                <input
                    id="profile-email"
                    v-model="profileForm.email"
                    type="email"
                    required
                    class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-900 text-sm shadow-xs focus:outline-none focus:ring-2 transition-all"
                    :class="errors.email ? 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500' : 'border-slate-300 focus:ring-blue-500/20 focus:border-blue-500'"
                />
                <p v-if="errors.email" class="text-xs text-rose-600 font-medium">{{ errors.email }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <button
                  type="submit"
                  :disabled="isProfileLoading"
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20 inline-flex items-center gap-2 cursor-pointer disabled:bg-slate-300 disabled:text-slate-500"
              >
                <svg v-if="isProfileLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Сохранить изменения
              </button>
              <span v-if="profileSuccess" class="text-sm text-emerald-600 font-medium flex items-center gap-1">✨ Данные обновлены</span>
            </div>
          </form>
        </div>

        <!-- Блок 2: Смена пароля -->
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
          <h3 class="text-lg font-bold text-slate-900 mb-1">Безопасность</h3>
          <p class="text-sm text-slate-500 mb-6">Регулярно меняйте пароль для защиты доступа к интеграциям.</p>

          <form @submit.prevent="handleChangePassword" class="space-y-4">
            <div class="flex flex-col gap-1.5">
              <label for="current-password" class="text-xs font-semibold uppercase tracking-wider text-slate-500">Текущий
                пароль</label>
              <input
                  id="current-password"
                  v-model="passwordForm.current_password"
                  type="password"
                  required
                  placeholder="••••••••"
                  class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-900 text-sm shadow-xs focus:outline-none focus:ring-2 transition-all"
                  :class="passwordErrors.current_password ? 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500' : 'border-slate-300 focus:ring-blue-500/20 focus:border-blue-500'"
              />
              <p v-if="passwordErrors.current_password" class="text-xs text-rose-600 font-medium">
                {{ passwordErrors.current_password }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="flex flex-col gap-1.5">
                <label for="new-password" class="text-xs font-semibold uppercase tracking-wider text-slate-500">Новый
                  пароль</label>
                <input
                    id="new-password"
                    v-model="passwordForm.password"
                    type="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-900 text-sm shadow-xs focus:outline-none focus:ring-2 transition-all"
                    :class="passwordErrors.password ? 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500' : 'border-slate-300 focus:ring-blue-500/20 focus:border-blue-500'"
                />
                <p v-if="passwordErrors.password" class="text-xs text-rose-600 font-medium">{{
                    passwordErrors.password
                  }}</p>
              </div>

              <div class="flex flex-col gap-1.5">
                <label for="new-password-confirm" class="text-xs font-semibold uppercase tracking-wider text-slate-500">Подтверждение</label>
                <input
                    id="new-password-confirm"
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-900 text-sm shadow-xs focus:outline-none focus:ring-2 transition-all"
                    :class="(passwordErrors.password_confirmation || isPasswordMismatch) ? 'border-rose-300 focus:ring-rose-500/20 focus:border-rose-500' : 'border-slate-300 focus:ring-blue-500/20 focus:border-blue-500'"
                />
                <p v-if="isPasswordMismatch" class="text-xs text-rose-600 font-medium">Пароли не совпадают.</p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <button
                  type="submit"
                  :disabled="isPasswordLoading || isPasswordMismatch"
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20 inline-flex items-center gap-2 cursor-pointer disabled:bg-slate-300 disabled:text-slate-500"
              >
                <svg v-if="isPasswordLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Обновить пароль
              </button>
              <span v-if="passwordSuccess" class="text-sm text-emerald-600 font-medium flex items-center gap-1">🔒 Пароль успешно изменен</span>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</template>
<script setup>
import {ref, reactive, computed, onMounted} from 'vue';
import axios from 'axios';
import {useAuthStore} from "../store/auth.js";

const authStore = useAuthStore();

// Заглушка стора авторизации. Замените на импорт вашего реального Pinia/Vuex стора
/*const authStore = reactive({
  user: {name: 'Иван Иванов', email: 'admin@test.ru'}
});*/

const isProfileLoading = ref(false);
const profileSuccess = ref(false);
const errors = ref({});

const isPasswordLoading = ref(false);
const passwordSuccess = ref(false);
const passwordErrors = ref({});

const profileForm = reactive({
  name: '',
  email: ''
});

const passwordForm = reactive({
  current_password: '', password: '', password_confirmation: ''
});

// Вычисление первых букв имени для аватара
const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U';
  return authStore.user.name.split(' ').map(n => n[0]).join('').slice(0, 2);
});

// Фронтенд проверка совпадения новых пароле
const isPasswordMismatch = computed(() => {
  return passwordForm.password_confirmation.length > 0 && passwordForm.password !== passwordForm.password_confirmation;
});

// Инициализация формы текущими данными
onMounted( async () => {
  if (authStore.user) {
    profileForm.name = authStore.user.name;
    profileForm.email = authStore.user.email;
  }
});
const handleUpdateProfile = async () => {
  isProfileLoading.value = true;
  errors.value = {};
  profileSuccess.value = false;
  try {
    const response = await axios.put('/api/user/profile-information', profileForm);
    if (response.status === 200) {
      authStore.user.name = profileForm.name;
      authStore.user.email = profileForm.email;
      profileSuccess.value = true;
      localStorage.setItem('user', JSON.stringify(authStore.user));
    }
  } catch (err) {
    if (err.response && err.response.status === 422) {
      errors.value = err.response.data.errors;
    }
  } finally {
    isProfileLoading.value = false;
  }
};

const handleChangePassword = async () => {
  if (isPasswordMismatch.value) return;
  isPasswordLoading.value = true;
  passwordErrors.value = {};
  passwordSuccess.value = false;
  try {
    const response = await axios.put('/api/user/password', passwordForm);
    if (response.status === 200) {
      passwordSuccess.value = true;
      passwordForm.current_password = '';
      passwordForm.password = '';
      passwordForm.password_confirmation = '';
    }
  } catch (err) {
    if (err.response && err.response.status === 422) {
      passwordErrors.value = err.response.data.errors;
    }
  } finally {
    isPasswordLoading.value = false;
  }
};
</script>