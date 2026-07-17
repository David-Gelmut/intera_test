<template>
  <aside
      v-if="authStore.isAuthenticated && authStore.isVerified"
      class="fixed inset-y-0 left-0 z-50 flex w-full flex-col border-r border-slate-200 bg-white shadow-xs transition-transform duration-300 lg:w-64 lg:translate-x-0"
      :class="{ 'translate-x-0': authStore.isMobileMenuOpen, '-translate-x-full': !authStore.isMobileMenuOpen }"
  >

    <!-- Шапка меню: Брендинг и Логотип -->
    <div class="flex h-16 items-center justify-between border-b border-slate-100 px-6">
      <div class="flex flex-row items-center justify-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white shadow-md shrink-0">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
          </svg>
        </div>

        <div class="flex flex-col min-w-0">
          <h2 class="text-base font-black text-slate-900 tracking-tight leading-none uppercase">City Of Masters</h2>
          <span class="text-[10px] text-blue-600 font-bold tracking-wider uppercase mt-1">Professional Network</span>
        </div>
      </div>

      <!-- Кнопка закрытия меню на мобильных устройствах -->
      <button @click="authStore.closeMobileMenu" class="text-slate-400 hover:text-slate-600 lg:hidden cursor-pointer">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Основная навигация по 5 модулям платформы -->
    <nav class="flex-1 space-y-1 px-4 py-4 overflow-y-auto flex flex-col justify-start">

      <router-link
          to="/dashboard"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <!-- Иконка дашборда / графиков / панелей -->
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
        </svg>
        <span>{{ authStore.user?.role === 'admin' ? 'Панель управления' : 'Дашборд' }}</span>
      </router-link>

      <!-- Пользователи (Видно ТОЛЬКО АДМИНУ) -->
      <router-link
          v-if="authStore.user?.role === 'admin'"
          to="/admin/users"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <!-- Иконка группы пользователей для администрирования -->
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span class="text-slate-700 font-semibold lg:font-medium">Управление пользователями</span>
      </router-link>


      <!-- Главная лента -->
      <router-link
          to="/feed"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <span>Главная лента</span>
      </router-link>

      <!-- Блоги и Статьи -->
      <router-link
          to="/blogs"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span>Блоги и Статьи</span>
      </router-link>

      <!-- Биржа заказов -->
      <router-link
          to="/jobs"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 00-2 2z" />
        </svg>
        <span>Биржа заказов</span>
      </router-link>

      <!-- Форум мастеров -->
      <router-link
          to="/forum"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
        </svg>
        <span>Форум мастеров</span>
      </router-link>

      <!-- Мессенджер (Сообщения) -->
      <router-link
          to="/chat"
          @click="authStore.closeMobileMenu"
          class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
          active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
        <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span>Сообщения</span>
      </router-link>

      <div class="w-full flex flex-col">
        <button
            @click="toggleToolsMenu"
            class="w-full flex flex-col lg:flex-row items-center justify-between rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer"
            :class="{ 'text-blue-600 lg:bg-slate-50/50 font-semibold': isToolsMenuOpen }"
        >
          <div class="flex flex-col lg:flex-row items-center">
            <!-- Иконка инструментов / ключа -->
            <svg class="mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Инструменты</span>
          </div>

          <!-- Стрелочка-индикатор (вращается при открытии) -->
          <svg
              class="h-4 w-4 hidden lg:block transition-transform duration-200"
              :class="{ 'rotate-180 text-blue-600': isToolsMenuOpen, 'text-slate-400': !isToolsMenuOpen }"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Вложенные пункты инструментов (Показываются, если истина) -->
        <div
            v-if="isToolsMenuOpen"
            class="mt-1 pl-0 lg:pl-8 space-y-1 transition-all"
        >
          <!-- А. Парсер данных -->
          <router-link
              to="/tools/convert"
              @click="authStore.closeMobileMenu"
              class="flex items-center justify-center lg:justify-start rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
              active-class="!text-blue-600 !font-bold"
          >
            <span class="mr-2 hidden lg:inline text-slate-300">•</span>
            <span>Конвертор</span>
          </router-link>

          <!-- Б. Конвертер текста -->
          <router-link
              to="/tools/settings"
              @click="authStore.closeMobileMenu"
              class="flex items-center justify-center lg:justify-start rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
              active-class="!text-blue-600 !font-bold"
          >
            <span class="mr-2 hidden lg:inline text-slate-300">•</span>
            <span>Парсер карт Яндекса</span>
          </router-link>

          <!-- В. Конвертер валют -->
<!--          <router-link
              to="/tools/currency"
              @click="authStore.closeMobileMenu"
              class="flex items-center justify-center lg:justify-start rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
              active-class="!text-blue-600 !font-bold"
          >
            <span class="mr-2 hidden lg:inline text-slate-300">•</span>
            <span>Конвертер валют</span>
          </router-link>-->
        </div>
      </div>

    </nav>

    <!-- Подвал меню: Профиль мастера и Выход -->
    <div class="border-t border-slate-100 p-4 space-y-3 bg-white">
      <router-link
          to="/profile"
          @click="authStore.closeMobileMenu"
          class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors group"
          active-class="!bg-slate-50"
      >
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 font-semibold text-sm text-blue-700 uppercase tracking-wider group-hover:bg-blue-200 transition-colors">
          {{ userInitials }}
        </div>

        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
            {{ authStore.user?.name || 'Пользователь' }}
          </p>
          <p class="text-xs text-slate-400 truncate">
            {{ authStore.user?.role ? authStore.user.role + ' • ' : '' }}Рейтинг: 5.0
          </p>
        </div>
      </router-link>

      <button
          @click="handleLogout"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/20"
      >
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        Выйти
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '../store/auth.js';
import { useRouter } from 'vue-router';
import { computed } from 'vue'
import { ref } from 'vue'

const authStore = useAuthStore();
const router = useRouter();
const userRole = computed(() => authStore.userRole);
const isToolsMenuOpen = ref(false);

function toggleToolsMenu() {
  isToolsMenuOpen.value = !isToolsMenuOpen.value;
}
const handleLogout = async () => {
  try {
    await authStore.logout();
  } catch (error) {
    console.error('Ошибка при выходе на сервере:', error);
  } finally {
    router.push('/login');
    window.location.href = '/login';
  }
};

const menuRoutes = computed(() => {
  return router.getRoutes().filter(route => {

    if (!userRole.value) return [];

    const isAuthRoute = route.meta?.auth;
    const isNotHidden = !route.meta?.hidden;

    const hasRoleAccess = !route.meta?.allowedRoles || route.meta?.allowedRoles.includes(userRole.value);

    return isAuthRoute && isNotHidden && hasRoleAccess;
  });
});

const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U';
  return authStore.user.name.charAt(0);
});
</script>

