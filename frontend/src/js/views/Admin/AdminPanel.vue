<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 font-sans">

    <!-- Навигация панели -->
    <nav class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-xl font-bold text-indigo-400 tracking-tight">Admin Area</span>
        <span class="text-xs font-medium px-2 py-0.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded">Панель управления</span>
      </div>
      <router-link to="/dashboard" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">
        Вернуться на сайт →
      </router-link>
    </nav>

    <!-- Основной блок -->
    <main class="max-w-4xl mx-auto mt-10 px-4">
      <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-xl space-y-6">
        <div>
          <h1 class="text-2xl font-bold text-white">Управление пользователями</h1>
          <p class="text-sm text-slate-400 mt-1">Здесь вы можете контролировать роли и статусы пользователей системы.</p>
        </div>

        <!-- Имитация таблицы/списка пользователей для демонстрации интерфейса -->
        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/50">
          <div class="p-4 border-b border-slate-700 bg-slate-900/80 text-xs font-semibold text-slate-400 uppercase tracking-wider grid grid-cols-3">
            <span>Пользователь</span>
            <span>Роль</span>
            <span>Статус</span>
          </div>
          <div class="divide-y divide-slate-700 text-sm">
            <div class="p-4 grid grid-cols-3 items-center">
              <span class="font-medium text-white">Вы (Администратор)</span>
              <div><span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs">admin</span></div>
              <div><span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs">active</span></div>
            </div>
            <div class="p-4 grid grid-cols-3 items-center text-slate-400">
              <span>Тестовый юзер</span>
              <div><span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs">user</span></div>
              <div><span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs">active</span></div>
            </div>
          </div>
        </div>



      </div>
    </main>



  </div>

</template>

<script setup>
// Специфичной логики здесь пока нет, страница полностью защищена на уровне Vue Router
import Sidebar from "../../components/Sidebar.vue";
import Header from "../../components/Header.vue";
import Footer from "../../components/Footer.vue";
</script>
