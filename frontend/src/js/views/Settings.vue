<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Карточка настроек -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Настройка интеграции</h2>
        <p class="text-sm text-gray-500 mb-6">Вставьте ссылку на карточку организации в Яндекс.Картах.</p>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="flex flex-col gap-1.5">
            <label for="yandex-url" class="text-sm font-medium text-gray-700">Ссылка на организацию</label>
            <input
                id="yandex-url"
                v-model="url"
                type="text"
                placeholder="https://yandex.ru"
                :disabled="isLoading"
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed text-sm"
            />
          </div>

          <button
              type="submit"
              :disabled="isLoading"
              class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-blue-400 disabled:cursor-not-allowed inline-flex justify-center items-center"
          >
            {{ isLoading ? 'Синхронизация...' : 'Получить данные организации' }}
          </button>
        </form>

        <!-- Уведомления -->
        <div v-if="errorMessage" class="mt-4 p-4 bg-red-50 border border-red-200 text-sm text-red-600 rounded-lg">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="mt-4 p-4 bg-green-50 border border-green-200 text-sm text-green-600 rounded-lg">
          {{ successMessage }}
        </div>
      </div>

      <!-- Карточка данных организации -->
      <div v-if="company" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-4">
          <h3 class="text-lg font-bold text-gray-900">{{ company.name }}</h3>
        </div>

        <!-- Сетка статистики -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 flex flex-col items-center justify-center text-center">
            <span class="text-2xl font-bold text-amber-700">⭐ {{ company.rating }}</span>
            <span class="text-xs text-amber-600 font-medium mt-1">Средний рейтинг</span>
          </div>
          <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 flex flex-col items-center justify-center text-center">
            <span class="text-2xl font-bold text-gray-900">{{ company.rating_count }}</span>
            <span class="text-xs text-gray-500 mt-1">Количество оценок</span>
          </div>
          <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 flex flex-col items-center justify-center text-center">
            <span class="text-2xl font-bold text-gray-900">{{ company.review_count }}</span>
            <span class="text-xs text-gray-500 mt-1">Количество отзывов</span>
          </div>
        </div>
      </div>

      <!-- Список отзывов -->
      <div v-if="reviews && reviews.length > 0" class="space-y-4">
        <div v-for="review in reviews" :key="review.id" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative">
          <span class="absolute top-4 right-4 text-xs font-mono text-gray-400">ID: {{ review.id }}</span>

          <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-3 text-sm">
            <strong class="font-semibold text-gray-900">{{ review.author_name }}</strong>
            <span class="hidden sm:inline text-gray-300">•</span>
            <span class="font-medium text-amber-600">Оценка: {{ review.rating }}/5</span>
            <span class="hidden sm:inline text-gray-300">•</span>
            <span class="text-gray-400 text-xs sm:text-sm">{{ review.published_at }}</span>
          </div>

          <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
            {{ review.text || 'Пользователь оставил только оценку, без текста отзыва.' }}
          </p>
        </div>
      </div>

      <!-- Пагинация -->
      <div v-if="totalPages > 1" class="flex items-center justify-between bg-white px-4 py-3 rounded-xl shadow-sm border border-gray-200 sm:px-6">
        <button
            :disabled="currentPage === 1 || isLoading"
            @click="changePage(currentPage - 1)"
            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Назад
        </button>

        <span class="text-sm text-gray-700 font-medium">
                    Страница <span class="text-gray-900">{{ currentPage }}</span> из <span class="text-gray-900">{{ totalPages }}</span>
                </span>

        <button
            :disabled="currentPage === totalPages || isLoading"
            @click="changePage(currentPage + 1)"
            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Вперед
        </button>
      </div>

    </div>
  </div>
</template>
<script setup>
import {ref} from 'vue';
import axios from 'axios';

const url = ref('');
const isLoading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const company = ref(null);
const reviews = ref([]);
const isReviewsLoading = ref(false);

const currentPage = ref(1);
const totalPages = ref(1);
const totalReviewsCount = ref(0);

const changePage = async (page) => {
    if (page < 1 || page > totalPages.value) return;
    isLoading.value = true;
    try {

        const response = await axios.post('/api/companies', {
            url: url.value,
            page: page
        });
        reviews.value = response.data.reviews.data;
        currentPage.value = response.data.reviews.current_page;
    } catch (error) {
        errorMessage.value = 'Не удалось загрузить страницу';
    } finally {
        isLoading.value = false;
    }
};

const handleSubmit = async () => {
        isLoading.value = true;
        successMessage.value = '';
        errorMessage.value = '';

        try {

            const response = await axios.post('/api/companies', { url: url.value });

            company.value = response.data.company;

            if(response.data.reviews){
                reviews.value = response.data.reviews.data;
                currentPage.value = response.data.reviews.current_page;
                totalPages.value = response.data.reviews.last_page;
                totalReviewsCount.value = response.data.reviews.total;
            }

            if (response.status === 202) {
                successMessage.value = 'Идёт синхронизация организации в фоновом режиме!';
            }

            if (response.status === 200) {
                successMessage.value = 'Организация успешно получена!';

            }

        } catch (error) {

            if (error.response && error.response.status === 422) {
                const validationErrors = error.response.data.errors;
                errorMessage.value = Object.values(validationErrors)[0][0];
            } else {
                errorMessage.value = 'Произошла ошибка при отправке запроса. Попробуйте позже.';
            }
        } finally {
            isLoading.value = false;
        }
    }
;
</script>
