<template>

  <div class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">

    <!-- Затемнение заднего фона при открытом мобильном меню -->
    <div
        v-if="authStore.isAuthenticated && authStore.isVerified && authStore.isMobileMenuOpen"
        @click="isMobileMenuOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden">

    </div>

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

  </div>
</template>
<script setup>
import { useAuthStore } from './store/auth.js';
const authStore = useAuthStore();
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
