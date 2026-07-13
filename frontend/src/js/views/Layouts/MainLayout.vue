
<template>
  <Sidebar />
  <div class="flex flex-col min-h-screen bg-slate-50 font-sans">
    <Header />
    <div class="flex flex-col flex-1">
      <div class="flex flex-1 flex-col transition-all duration-300">
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
  </div>
  <Footer />
</template>
<script setup>
import Sidebar from '../../components/Sidebar.vue';
import Header from '../../components/Header.vue';
import Footer from '../../components/Footer.vue';
import { useAuthStore } from '../../store/auth.js';

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