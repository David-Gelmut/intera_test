<template>
  <header
      v-if="authStore.isAuthenticated && authStore.isVerified"
      class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-xs lg:hidden">

    <div class="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </span>
      <span>City Of Masters</span>
    </div>

    <!-- Кнопка открытия мобильного меню -->
    <button
        @click="isMobileMenuOpen = true"
        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none">
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </header>
</template>
<script setup>
import { useAuthStore } from '../store/auth.js';
import { useRouter } from 'vue-router';
import { computed, ref } from 'vue'

const authStore = useAuthStore();
const router = useRouter();
const isMobileMenuOpen = ref(false);

</script>