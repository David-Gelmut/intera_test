<template>
  <aside
      v-if="authStore.isAuthenticated && authStore.isVerified"
      class="fixed inset-y-0 left-0 z-50 flex w-full flex-col border-r border-slate-200 bg-white shadow-xs transition-transform duration-300 lg:w-64 lg:translate-x-0"
      :class="{ 'translate-x-0': authStore.isMobileMenuOpen, '-translate-x-full': !authStore.isMobileMenuOpen }"
  >

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

      <button @click="authStore.closeMobileMenu" class="text-slate-400 hover:text-slate-600 lg:hidden cursor-pointer">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

    </div>

    <nav class="flex-1 space-y-2 px-4 py-6 overflow-y-auto flex flex-col justify-center lg:justify-start lg:space-y-1">
      <router-link
          v-for="route in menuRoutes"
          :key="route.path"
          :to="route.path"
          @click="authStore.closeMobileMenu"
      class="flex flex-col lg:flex-row items-center justify-center lg:justify-start rounded-xl px-4 py-4 lg:py-2.5 text-base lg:text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-center lg:text-left"
      active-class="!bg-blue-50 !text-blue-700 !font-semibold shadow-xs"
      >
      <svg class="mr-3 mb-2 lg:mb-0 lg:mr-3 h-6 w-6 lg:h-5 lg:w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
      </svg>
      <span>{{ route.name }}</span>
      </router-link>
    </nav>

    <div class="border-t border-slate-100 p-4 space-y-3 bg-white">

      <router-link
          to="/profile"
          @click="authStore.closeMobileMenu"
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


      <button
          @click="handleLogout"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/20">

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

const authStore = useAuthStore();
const router = useRouter();

const userRole = computed(() => authStore.userRole);

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

