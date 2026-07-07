<template>
  <div class="flex min-h-screen bg-slate-50 font-sans">
    <Sidebar />

    <div class="flex flex-col flex-1">

      <Header />

      <div
          class="flex flex-1 flex-col transition-all duration-300"
          :class="{ 'lg:pl-64': authStore.isAuthenticated && authStore.isVerified }">
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

      <Footer />

    </div>
  </div>
</template>
<script setup>
import Sidebar from '../../components/Sidebar.vue';
import Header from '../../components/Header.vue';
import Footer from '../../components/Footer.vue';
import { useAuthStore } from '../../store/auth.js';

const authStore = useAuthStore();

/*import { useRouter } from 'vue-router';
import { computed, ref } from 'vue'


const router = useRouter();


// Состояние открытия мобильного меню
const isMobileMenuOpen = ref(false);

// Текущий год для футера
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