<template>
  <div class="space-y-6 font-sans pb-10">

    <!-- Верхний блок: Шапка дашборда -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Рабочий стол мастера</h1>
        <p class="text-sm text-slate-500 mt-1">Аналитика вашего профиля и активности на платформе City Of Masters</p>
      </div>
      <div class="text-xs font-semibold px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 w-fit shrink-0">
        Статус профиля: {{ userStatus || 'Верифицирован' }}
      </div>
    </div>

    <!-- Блок 1: Системные данные профиля текущего пользователя -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- А. Ваш Email -->
      <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs">
        <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Ваш Email</span>
        <span class="text-sm font-bold text-slate-700 block mt-1.5 break-all">{{ userEmail }}</span>
      </div>

      <!-- Б. Роль в системе -->
      <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs">
        <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Роль в системе</span>
        <div class="mt-1.5">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize"
                :class="userRole === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-blue-50 text-blue-700 border border-blue-100'">
            {{ userRole }}
          </span>
        </div>
      </div>

      <!-- В. Статус аккаунта -->
      <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs">
        <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Статус аккаунта</span>
        <div class="mt-1.5">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 capitalize">
            Активен
          </span>
        </div>
      </div>
    </div>

    <!-- Блок 2: Бизнес-метрики (Мини-карточки счетчиков) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Просмотры -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl shrink-0">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </div>
        <div class="min-w-0">
          <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider truncate">Просмотры (7 дн)</span>
          <span class="text-2xl font-black text-slate-900 leading-none mt-1 block">{{ stats.total_views || 0 }}</span>
        </div>
      </div>

      <!-- Активные сделки на бирже -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="min-w-0">
          <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider truncate">Активные сделки</span>
          <span class="text-2xl font-black text-slate-900 leading-none mt-1 block">3 заказа</span>
        </div>
      </div>
    </div>

    <!-- Блок 3: Главная секция интерактивной аналитики (График + Гео-локация) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Левая часть: Линейный график визитов (Занимает 2 колонки на десктопе) -->
      <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col">
        <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-tight">Динамика визитов в профиль</h3>
        <div class="h-64 w-full relative flex-1">
          <LineChart v-if="loaded" :data="chartData" :options="chartOptions" />
          <div v-else class="absolute inset-0 flex items-center justify-center text-xs text-slate-400">
            Загрузка аналитики...
          </div>
        </div>
      </div>

      <!-- Правая часть: География посетителей (Занимает 1 колонку) -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between gap-6">
        <div>
          <h3 class="text-sm font-bold text-slate-800 mb-2 uppercase tracking-tight">География интереса</h3>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Города заказчиков, которые чаще всего открывали ваши контакты на этой неделе.</p>

          <div class="space-y-3">
            <div v-for="(item, idx) in stats.top_cities" :key="idx" class="flex items-center justify-between border-b border-slate-50 pb-2 last:border-0 last:pb-0">
              <span class="text-sm font-semibold text-slate-700 flex items-center gap-1.5 truncate">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>{{ item.city }}
              </span>
              <span class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md shrink-0">{{ item.total }} визитов</span>
            </div>
            <div v-if="!stats.top_cities?.length" class="text-center py-8 text-xs text-slate-400 italic">
              Пока нет данных по городам
            </div>
          </div>
        </div>

        <router-link to="/profile" class="w-full py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200/60 text-center block transition-colors cursor-pointer">
          Редактировать портфолио
        </router-link>
      </div>
    </div>

  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Line as LineChart } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale } from 'chart.js';
import { useRouter } from 'vue-router';

const router = useRouter();
const userName = ref('');
const userEmail = ref('');
const userRole = ref('');
const userStatus = ref('');

// Регистрируем модули Chart.js
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale);

const loaded = ref(false);
const stats = ref({});
const chartData = ref(null);

// Кастомные стили для графика под дизайн Tailwind
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: { grid: { display: true, color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 10 } } },
    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
  },
  plugins: {
    legend: { display: false } // скрываем легенду, так как у нас один график
  }
};

const handleLogout = async () => {
  try {
    await axios.post('/api/logout');
  } finally {
    localStorage.removeItem('user');
    router.push({ name: 'Login' });
  }
};

onMounted(async () => {

  const userJson = localStorage.getItem('user');
  if (userJson) {
    const user = JSON.parse(userJson);
    userName.value = user.name;
    userEmail.value = user.email;
    userRole.value = user.role;
    userStatus.value = user.status;
  }

  try {
    const response = await axios.get('/api/dashboard/statistics');
    stats.value = response.data;

    // Собираем массив данных для визуализации кривой
    chartData.value = {
      labels: response.data.labels,
      datasets: [
        {
          label: 'Просмотры',
          backgroundColor: '#3b82f6', // Синий цвет точек (blue-500)
          borderColor: '#2563eb',     // Синяя линия графика (blue-600)
          borderWidth: 2.5,
          tension: 0.3,               // Плавное скругление изгибов линии
          pointRadius: 4,
          data: response.data.values
        }
      ]
    };
    loaded.value = true;
  } catch (error) {
    console.error('Ошибка загрузки статистики дашборда:', error);
  }
});
</script>
