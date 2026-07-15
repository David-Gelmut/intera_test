<template>
  <!-- Фиксированный контейнер в правом нижнем углу -->
  <div class="fixed bottom-5 right-5 z-50 w-full max-w-sm space-y-3 pointer-events-none">
    <transition-group
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
      <div
          v-for="toast in toastStore.toasts"
          :key="toast.id"
          class="pointer-events-auto flex w-full items-start gap-3 rounded-xl bg-white p-4 shadow-xl border border-slate-100 font-sans"
      >
        <!-- Иконка сообщения (Конверт) -->
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>

        <!-- Контент текста -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-slate-900 truncate">{{ toast.title }}</p>
          <p class="mt-0.5 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ toast.message }}</p>
        </div>

        <!-- Кнопка закрытия (крестик) -->
        <button
            @click="toastStore.remove(toast.id)"
            class="text-slate-400 hover:text-slate-600 cursor-pointer rounded-md focus:outline-none"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToastStore } from '../store/toast.js';
const toastStore = useToastStore();
</script>
