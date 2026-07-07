<template>
  <div class="min-h-screen bg-slate-50 font-sans">
    <!-- Навигация -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
      <span class="text-xl font-bold text-slate-800 tracking-tight">MyProject</span>
      <div class="flex items-center gap-4">
        <!-- Кнопка админки видна ТОЛЬКО если у пользователя роль 'admin' -->
        <router-link
            v-if="userRole === 'admin'"
            to="/admin/panel"
            class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors"
        >
          Админ-панель
        </router-link>

        <button
            @click="handleLogout"
            class="text-sm font-medium text-slate-600 hover:text-rose-600 transition-colors cursor-pointer"
        >
          Выйти
        </button>
      </div>
    </nav>

    <!-- Основной контент -->
    <main class="max-w-4xl mx-auto mt-10 px-4">
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Добро пожаловать, {{ userName }}!</h1>
          <p class="text-sm text-slate-500 mt-1">Вы успешно авторизовались в системе.</p>
        </div>

        <!-- Карточки со статусами -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
            <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Ваш Email</span>
            <span class="text-sm font-semibold text-slate-700 block mt-1 break-all">{{ userEmail }}</span>
          </div>

          <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
            <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Роль в системе</span>
            <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="userRole === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'">
              {{ userRole }}
            </span>
          </div>

          <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
            <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Статус аккаунта</span>
            <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 capitalize">
              {{ userStatus }}
            </span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const userName = ref('');
const userEmail = ref('');
const userRole = ref('');
const userStatus = ref('');

onMounted(() => {
  const userJson = localStorage.getItem('user');
  if (userJson) {
    const user = JSON.parse(userJson);
    userName.value = user.name;
    userEmail.value = user.email;
    userRole.value = user.role;
    userStatus.value = user.status;
  }
});

const handleLogout = async () => {
  try {
    await axios.post('/api/logout');
  } finally {
    localStorage.removeItem('user');
    router.push({ name: 'Login' });
  }
};
</script>
