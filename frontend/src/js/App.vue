<template>
  <div class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">

    <!-- Верхняя шапка для мобильных устройств (показывается только авторизованным) -->
<!--    <header
        v-if="authStore.isAuthenticated && authStore.isVerified"
        class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-xs lg:hidden">

      <div class="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </span>
        <span>City Of Masters</span>
      </div>

      &lt;!&ndash; Кнопка открытия мобильного меню &ndash;&gt;
      <button
          @click="isMobileMenuOpen = true"
          class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </header>-->

    <!-- Затемнение заднего фона при открытом мобильном меню -->
    <div
        v-if="authStore.isAuthenticated && authStore.isVerified && isMobileMenuOpen"
        @click="isMobileMenuOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden"></div>

    <!-- Боковая навигация (Sidebar) — адаптивная -->
<!--    <aside
        v-if="authStore.isAuthenticated && authStore.isVerified"
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200 bg-white shadow-xs transition-transform duration-300 lg:translate-x-0"
        :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">

      &lt;!&ndash; Логотип / Название проекта &ndash;&gt;
      <div class="flex h-16 items-center justify-between border-b border-slate-100 px-6">

        <div class="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white shadow-xs">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </span>
          <span>City Of Masters</span>
        </div>

        &lt;!&ndash; Кнопка закрытия внутри сайдбара (только для мобилок) &ndash;&gt;
        <button @click="isMobileMenuOpen = false" class="text-slate-400 hover:text-slate-600 lg:hidden">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

      </div>

      &lt;!&ndash; Меню навигации &ndash;&gt;
      <nav class="flex-1 space-y-1 px-4 py-6 overflow-y-auto">
        <router-link
            v-for="route in menuRoutes"
            :key="route.path"
            :to="route.path"
            @click="isMobileMenuOpen = false"
            class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
            active-class="!bg-blue-50 !text-blue-700 !font-semibold"
        >
          <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
          </svg>
          {{ route.name }}
        </router-link>
      </nav>

      &lt;!&ndash; Подвал сайдбара с Профилем и кнопкой Выхода &ndash;&gt;
      <div class="border-t border-slate-100 p-4 space-y-3 bg-white">
        &lt;!&ndash; Блок Профиля &ndash;&gt;
        <router-link
            to="/profile"
            @click="isMobileMenuOpen = false"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors group"
            active-class="!bg-slate-50">

          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 font-semibold text-sm text-blue-700 uppercase tracking-wider group-hover:bg-blue-200 transition-colors">
            {{ userInitials }}
          </div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
              {{ authStore.user?.name || 'Пользователь' }}
            </p>
            <p class="text-xs text-slate-400 truncate">
              {{ authStore.user?.email || 'email@example.com' }}
            </p>
          </div>

        </router-link>

        &lt;!&ndash; Кнопка Выхода &ndash;&gt;
        <button
            @click="handleLogout"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/20">

          <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Выйти
        </button>
      </div>
    </aside>-->

    <!-- Основной контейнер контента -->
    <div
        class="flex flex-1 flex-col transition-all duration-300"
        :class="{ 'lg:pl-64': authStore.isAuthenticated && authStore.isVerified }">
      <!-- Контент страницы -->
      <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
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

    <!-- Подвал сайта (Footer) с контактами -->
<!--    <footer v-if="authStore.isAuthenticated && authStore.isVerified" class="mt-auto border-t border-slate-200 bg-white py-6 px-4 md:px-8 text-center sm:text-left">
      <div class="max-w-7xl w-full mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs md:text-sm text-slate-500">
        <div>
          <p class="font-semibold text-slate-700">&copy; {{ currentYear }} City Of Masters. Все права защищены.</p>
        </div>

        &lt;!&ndash; Контактные данные &ndash;&gt;
        <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
          <a href="mailto:support@interamaps.com" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            davidgelmut12345@gmail.com
          </a>
          <a href="tel:+78005553535" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            8 (963) 882-48-00
          </a>
        </div>
      </div>
    </footer>-->


  </div>
</template>
<script setup>
import { useAuthStore } from './store/auth.js';
import { computed, ref } from 'vue'
//import { useRouter } from 'vue-router';
const authStore = useAuthStore();
const isMobileMenuOpen = ref(false);

//const router = useRouter();
/*// Текущий год для футера
const currentYear = computed(() => new Date().getFullYear());

const menuRoutes = computed(() => {
    return router.getRoutes().filter(route => route.meta?.auth && !route.meta?.hidden)
})

// Вычисляемое свойство для первой буквы имени (для круглого аватара)
const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U';
  // Берем первую букву имени
  return authStore.user.name.charAt(0);
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};*/
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
