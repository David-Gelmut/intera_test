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

<!--        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-4 font-bold text-2xl uppercase tracking-wider">
          {{ userInitials }}
        </div>-->


        <div class="flex flex-col items-center sm:flex-row gap-5 p-4 bg-slate-50 rounded-2xl border border-slate-100">

          <div class="relative h-20 w-20 shrink-0 select-none group">

            <!-- Круглый аватар (Клик по нему увеличивает фото) -->
            <div
                @click="isAvatarModalOpen = true"
                class="flex h-full w-full items-center justify-center rounded-full bg-indigo-100 font-semibold text-xl text-indigo-700 uppercase tracking-wider overflow-hidden border border-indigo-200/50 shadow-2xs cursor-pointer"
                title="Просмотреть фото"
            >
              <img
                  v-if="authStore.user?.avatar_path"
                  :src="authStore.user.avatar_path"
                  alt="Аватар"
                  class="h-full w-full object-cover rounded-full transition-transform duration-200 group-hover:scale-105"
              />
              <template v-else>
                {{ authStore.user?.name ? authStore.user.name.charAt(0) : 'U' }}
              </template>
            </div>

            <!-- [НОВОЕ] Кнопка ПЛЮС для загрузки нового фото (Позиционируется внизу справа) -->
            <button
                type="button"
                @click="triggerFileInput"
                class="absolute bottom-0 right-0 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-sm shadow-sm border-2 border-white hover:bg-blue-700 hover:scale-110 active:scale-95 transition-all cursor-pointer focus:outline-none"
                title="Загрузить новое фото"
            >
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </button>

            <!-- Скрытый системный input для выбора файла (Код из прошлых шагов) -->
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onAvatarChange"
            />
          </div>




          <!-- Интерактивный контейнер Аватара -->
<!--          <div
              @click="triggerFileInput"
              class="relative h-20 w-20 rounded-full border-2 border-white shadow-sm cursor-pointer group overflow-hidden bg-blue-100 flex items-center justify-center shrink-0"
          >
            &lt;!&ndash; 1. Если аватар ЗАГРУЖЕН в базу &ndash;&gt;
            <img
                v-if="authStore.user?.avatar_path"
                :src="authStore.user.avatar_path"
                alt="Аватар"
                class="h-full w-full object-cover transition-transform group-hover:scale-105"
            />

            &lt;!&ndash; 2. Если аватара НЕТ (Заглушка с первой буквой имени) &ndash;&gt;
            <span v-else class="text-xl font-bold text-blue-700 uppercase">
              {{ authStore.user?.name ? authStore.user.name.charAt(0) : 'U' }}
            </span>

            &lt;!&ndash; Прозрачный слой "Обновить фото" при наведении (Hover) &ndash;&gt;
            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-bold">
              <svg class="h-5 w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              <span>{{ isUploading ? 'Загрузка...' : 'Изменить' }}</span>
            </div>

            &lt;!&ndash; Скрытый системный тег ввода файла &ndash;&gt;
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onAvatarChange"
            />
          </div>-->

          <!-- Текстовый блок рядом с аватаром -->
          <div class="text-center sm:text-left min-w-0">
<!--            <h3 class="text-base font-bold text-slate-900 truncate">{{ authStore.user?.name }}</h3>
            <p class="text-xs text-slate-400 mt-0.5 truncate">{{ authStore.user?.email }}</p>-->
            <button @click="triggerFileInput" type="button" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors mt-2 cursor-pointer block mx-auto sm:mx-0">
              Загрузить новую фотографию
            </button>
          </div>

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

    <!-- МОДАЛЬНОЕ ОКНО ДЛЯ ПРОСМОТРА АВАТАРА НА ВЕСЬ ЭКРАН -->
    <div
        v-if="isAvatarModalOpen && authStore.user?.avatar_path"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4"
        @click="isAvatarModalOpen = false"
    >
      <!-- Крестик для закрытия -->
      <button
          type="button"
          @click="isAvatarModalOpen = false"
          class="absolute top-4 right-4 p-2 text-white/70 hover:text-white rounded-full bg-white/10 hover:bg-white/20 transition-colors cursor-pointer"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Крупное изображение (Ограничено по высоте экрана, чтобы не ломать верстку) -->
      <div class="max-w-md w-full max-h-[80vh] p-2 bg-white rounded-3xl shadow-2xl overflow-hidden">
        <img
            :src="authStore.user.avatar_path"
            alt="Большой аватар"
            class="w-full h-auto max-h-[75vh] object-cover rounded-2xl shadow-inner"
        />
      </div>
    </div>




  </div>
</template>
<script setup>
import {ref, reactive, computed, onMounted} from 'vue';
import axios from 'axios';
import {useAuthStore} from "../../store/auth.js";


const authStore = useAuthStore();
const fileInput = ref(null);
const isUploading = ref(false)

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
const isAvatarModalOpen = ref(false);

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

// Загрузка файла на бэкенд
async function onAvatarChange(event) {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('avatar', file);

  try {
    isUploading.value = true;
    const response = await axios.post('/api/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.success) {
      // Обновляем данные пользователя локально в Pinia-сторе
      authStore.user.avatar_path = response.data.avatar_url;

      alert('Аватар успешно обновлен!');
    }
  } catch (error) {
    console.error('Ошибка при загрузке аватара:', error);
    alert('Не удалось загрузить изображение. Проверьте формат и размер.');
  } finally {
    isUploading.value = false;
    authStore.getUser();
  }
}


// Триггер клика по скрытому input
function triggerFileInput() {
  if (fileInput.value) fileInput.value.click();
}

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
     /* authStore.user.name = profileForm.name;
      authStore.user.email = profileForm.email;
      profileSuccess.value = true;
      localStorage.setItem('user', JSON.stringify(authStore.user));*/
      const userResponse = await axios.get('/api/user');
      authStore.user = userResponse.data;
      localStorage.setItem('user', JSON.stringify(userResponse.data));
      profileSuccess.value = true;
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