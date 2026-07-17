<template>
  <header
      v-if="authStore.isAuthenticated && authStore.isVerified"
      class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-xs lg:hidden">

    <div class="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
          </svg>
        </span>
      <span>City Of Masters</span>
    </div>

    <!-- Кнопка открытия мобильного меню -->
    <button
        @click.prevent="authStore.toggleMobileMenu"
        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none">
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </header>
</template>
<script setup>
import { useAuthStore } from '../store/auth.js';

const authStore = useAuthStore();

</script>