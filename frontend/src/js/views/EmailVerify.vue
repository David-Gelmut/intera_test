<template>
  <div style="text-align: center; margin-top: 50px; font-family: sans-serif;">
    <h2>{{ statusMessage }}</h2>
    <p v-if="loading">Пожалуйста, подождите, проверяем данные...</p>
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const statusMessage = ref('Обработка ссылки подтверждения...');

onMounted(async () => {
  // Достаем закодированный URL бэкенда из query параметров (?url=...)
  const backendUrl = route.query.url;

  if (!backendUrl) {
    statusMessage.value = 'Ошибка: Неверная или отсутствующая ссылка.';
    loading.value = false;
    return;
  }

  try {
    // Делаем фоновый запрос на бэк по подписанной ссылке Laravel
    // Браузер сам передаст куки сессии, так как запрос идет через Axios
    await axios.get(backendUrl);

    statusMessage.value = 'Email успешно подтвержден! Обновляем профиль...';

    // Запрашиваем свежие данные пользователя с бэка, чтобы обновить email_verified_at
    const userResponse = await axios.get('/api/user');
    console.log(userResponse);
    if (userResponse.data) {
      localStorage.setItem('user', JSON.stringify(userResponse.data));
      // Через 2 секунды перекидываем в профиль
      setTimeout(() => {
        router.push('/profile');
      }, 2000);
    }


  } catch (error) {
    console.error(error);
    statusMessage.value = 'Не удалось подтвердить Email. Возможно, сессия истекла или ссылка устарела.';
  } finally {
    loading.value = false;
  }
});
</script>
