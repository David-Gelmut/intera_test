<template>
  <footer v-if="authStore.isAuthenticated && authStore.isVerified" class="w-full mt-auto border-t border-slate-200 bg-white py-6 px-4 md:px-8 text-center sm:text-left">
    <div class="max-w-7xl w-full mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs md:text-sm text-slate-500">
      <div>
        <p class="font-semibold text-slate-700">&copy; {{ currentYear }} City Of Masters. Все права защищены.</p>
      </div>

      <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
        <a href="mailto:davidgelmut12345@gmail.com" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          davidgelmut12345@gmail.com
        </a>
        <a href="tel:+79638824800" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
          </svg>
          8 (963) 882-48-00
        </a>
      </div>
    </div>
  </footer>
</template>
<script setup>
import { useAuthStore } from '../store/auth.js';
import { useRouter } from 'vue-router';
import { computed, ref } from 'vue'

const authStore = useAuthStore();
const router = useRouter();
//const isMobileMenuOpen = ref(false);
const currentYear = computed(() => new Date().getFullYear());


</script>
