
<template>
  <Sidebar />
  <div class="flex flex-col min-h-screen bg-slate-50 font-sans">
    <Header />
    <div class="flex flex-col flex-1">
      <div class="flex flex-1 flex-col transition-all duration-300">
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </main>
      </div>
    </div>
  </div>
  <Footer />
  <ToastContainer />
</template>
<script setup>
import { useRouter } from 'vue-router';
import Sidebar from '../../components/Sidebar.vue';
import Header from '../../components/Header.vue';
import Footer from '../../components/Footer.vue';
import { useAuthStore } from '../../store/auth.js';
import { useToastStore } from '../../store/toast.js';
import { useChatStore } from '../../store/chat.js';
import ToastContainer from '../../components/ToastContainer.vue';
import {onMounted, onUnmounted} from "vue";

const authStore = useAuthStore();
const toastStore = useToastStore();
const chatStore = useChatStore();
const router = useRouter();

onMounted(() => {
  const currentUserId = authStore.user?.id;

  if (currentUserId && window.Echo) {
    window.Echo.private(`user.${currentUserId}`)
        .listen('MessageSent', (e) => {
          console.log('Глобальное фоновое уведомление:', e);

          chatStore.incrementUnreadCount(e.chat_id);

        //  const isChatPage = router.currentRoute.value.name === 'ChatPage';

         // if (!isChatPage) {
            toastStore.add(`Новое сообщение от ${e.user_name}`, e.text, 'info');
         /// }
        });
  }
});

onUnmounted(() => {
  if (authStore.user?.id && window.Echo) {
    window.Echo.leaveChannel(`user.${authStore.user.id}`);
  }
});
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