<template>
  <div class="flex min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">

    <!-- Боковая навигация (Sidebar) — показывается только авторизованным юзерам -->
    <aside
        v-if="authStore.isAuthenticated"
        class="fixed inset-y-0 left-0 z-20 flex w-64 flex-col border-r border-slate-200 bg-white shadow-xs"
    >
      <!-- Логотип / Название проекта -->
      <div class="flex h-16 items-center border-b border-slate-100 px-6">
        <div class="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white shadow-xs">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </span>
          <span>Intera Maps</span>
        </div>
      </div>

      <!-- Меню навигации -->
      <nav class="flex-1 space-y-1 px-4 py-6">
        <router-link
            v-for="route in menuRoutes"
            :key="route.path"
            :to="route.path"
            class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
            active-class="!bg-blue-50 !text-blue-700 !font-semibold"
        >
          <!-- Универсальная SVG иконка папки вместо эмодзи -->
          <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
          </svg>
          {{ route.name }}
        </router-link>
      </nav>

      <!-- Подвал сайдбара с кнопкой Выхода -->
      <div class="border-t border-slate-100 p-4">
        <button
            @click="handleLogout"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/20"
        >
          <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Выйти
        </button>
      </div>
    </aside>

    <!-- Основной контейнер контента -->
    <div
        class="flex flex-1 flex-col transition-all duration-300"
        :class="{ 'pl-64': authStore.isAuthenticated }"
    >
      <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
        <!-- Анимация плавного появления страниц при роутинге -->
        <router-view v-slot="{ Component }">
          <transition
              name="fade"
              mode="out-in"
          >
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>

  </div>
</template>
<script setup>
import { useAuthStore } from './store/auth.js';
import { useRouter } from 'vue-router';
import { computed } from 'vue'

const authStore = useAuthStore();
const router = useRouter();

const menuRoutes = computed(() => {
    return router.getRoutes().filter(route => route.meta?.auth)
})

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
