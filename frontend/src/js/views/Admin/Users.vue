<template>
  <div class="space-y-6 font-sans">
    <!-- Заголовок страницы -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Управление пользователями</h1>
        <p class="text-sm text-slate-500 mt-1">Просмотр списка, изменение ролей и блокировка учетных записей.</p>
      </div>
      <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-indigo-100">
        Всего: {{ users.length }}
      </span>
    </div>

    <!-- Индикатор загрузки -->
    <div v-if="loading" class="flex justify-center py-12 text-slate-500 text-sm">
      <svg class="animate-spin h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Загрузка списка пользователей...
    </div>

    <!-- Таблица пользователей -->
    <div v-else class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[500px] lg:min-w-full">
          <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
            <th class="p-4">Пользователь</th>
            <!-- Скрываем отдельную колонку Email на мобилках до брейкпоинта md -->
            <th class="p-4 hidden md:table-cell">Email</th>
            <th class="p-4">Подтвержден</th>
            <th class="p-4">Роль</th>
            <th class="p-4">Статус</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
          <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/80 transition-colors">

            <!-- Имя, ID и объединенный Email -->
            <td class="p-4 font-medium text-slate-900 max-w-[180px] sm:max-w-none">
              <div class="truncate sm:whitespace-normal">{{ user.name }}</div>

              <!-- На мобильных устройствах email выводится здесь аккуратной непереносимой строкой -->
              <span class="text-xs text-slate-500 block font-normal md:hidden truncate max-w-[160px] sm:max-w-xs mt-0.5">
                {{ user.email }}
              </span>

              <span class="text-xs text-slate-400 block font-normal mt-0.5">ID: {{ user.id }}</span>
            </td>

            <!-- Email (Классическая колонка для больших экранов) -->
            <!-- Скрываем на мобилках (hidden), показываем от md и выше (md:table-cell). Добавлен whitespace-nowrap, чтобы адрес никогда не разрывало -->
            <td class="p-4 text-slate-600 hidden md:table-cell whitespace-nowrap">{{ user.email }}</td>

            <!-- Статус Email верификации -->
            <td class="p-4 whitespace-nowrap">
              <span v-if="user.email_verified_at" class="inline-flex items-center gap-1 text-xs text-emerald-700 font-medium bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">
                Да
              </span>
              <span v-else class="inline-flex items-center gap-1 text-xs text-amber-700 font-medium bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-md">
                Нет
              </span>
            </td>

            <!-- Изменение Роли (Enum) -->
            <td class="p-4">
              <select
                  v-model="user.role"
                  @change="updateUser(user)"
                  :disabled="isCurrentUser(user.id)"
                  class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs font-medium text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer"
              >
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
              </select>
            </td>

            <!-- Изменение Статуса -->
            <td class="p-4">
              <select
                  v-model="user.status"
                  @change="updateUser(user)"
                  :disabled="isCurrentUser(user.id)"
                  class="rounded-lg border text-xs font-medium px-2 py-1 focus:outline-none focus:ring-1 disabled:opacity-50 cursor-pointer"
                  :class="user.status === 'active'
                    ? 'border-emerald-300 text-emerald-700 bg-emerald-50 focus:border-emerald-500 focus:ring-emerald-500'
                    : 'border-rose-300 text-rose-700 bg-rose-50 focus:border-rose-500 focus:ring-rose-500'"
              >
                <option value="active">Active</option>
                <option value="banned">Banned</option>
              </select>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
import { useAuthStore } from '../../store/auth.js';
import { useRouter } from 'vue-router';
import {computed, onMounted, ref} from 'vue'

const authStore = useAuthStore();
const router = useRouter();
const isMobileMenuOpen = ref(false);
const userRole = authStore.userRole;

const users = ref([]);
const loading = ref(true);
let currentUserId = null;

// Определяем ID администратора, который сейчас залогинен, чтобы он случайно не забанил себя
onMounted(() => {
  const userJson = localStorage.getItem('user');
  if (userJson) {
    currentUserId = JSON.parse(userJson).id;
  }
  fetchUsers();
});

const isCurrentUser = (id) => id === currentUserId;

// Запрос списка пользователей с бэкенда
const fetchUsers = async () => {
  try {
    const response = await axios.get('/api/users');
    console.log('Данные с бэка:', response.data);
    users.value = response.data;
  } catch (error) {
    console.error('Не удалось загрузить пользователей:', error);
  } finally {
    loading.value = false;
  }
};

// Отправка измененных данных на бэкенд при смене значения в select
const updateUser = async (user) => {
  try {
    await axios.put(`/api/users/${user.id}`, {
      role: user.role,
      status: user.status
    });
    alert(`Данные пользователя ${user.name} успешно обновлены!`);
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || 'Ошибка при обновлении пользователя.');
    // В случае ошибки возвращаем список в исходное состояние
    fetchUsers();
  }
};
</script>
