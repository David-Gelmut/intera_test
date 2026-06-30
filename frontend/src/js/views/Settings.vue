<template>
    <div class="settings-page">
        <div class="container">

            <div class="settings-card">
                <h2>Настройка интеграции</h2>
                <p class="subtitle">Вставьте ссылку на карточку организации в Яндекс.Картах.</p>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="yandex-url">Ссылка на организацию</label>
                        <input
                            id="yandex-url"
                            v-model="url"
                            type="text"
                            placeholder="https://yandex.ru"
                            :disabled="isLoading"
                        />
                    </div>
                    <button type="submit" :disabled="isLoading">
                        {{ isLoading ? 'Синхронизация...' : 'Получить данные организации' }}
                    </button>
                </form>
                <div v-if="errorMessage" class="error-alert">{{ errorMessage }}</div>
                <div v-if="successMessage" class="success-alert">{{ successMessage }}</div>
            </div>

            <div v-if="company" class="data-card">
                <div class="company-header"><h3>{{ company.name }}</h3></div>
                <div class="stats-grid">
                    <div class="stat-box rating">
                        <span class="stat-val">⭐ {{ company.rating }}</span>
                        <span class="stat-lbl">Средний рейтинг</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-val">{{ company.rating_count }}</span>
                        <span class="stat-lbl">Количество оценок</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-val">{{ company.review_count }}</span>
                        <span class="stat-lbl">Количество отзывов</span>
                    </div>
                </div>
            </div>

            <div v-if="reviews && reviews.length > 0" class="reviews-list">
                <div v-for="review in reviews" :key="review.id" class="review-item">
                    <span class="">ID: {{ review.id }}</span>
                    <div class="review-meta">
                        <strong class="author">{{ review.author_name }}</strong>
                        <span class="stars">Оценка: {{ review.rating }}/5</span>
                        <span class="date">{{ review.published_at }}</span>
                    </div>
                    <p class="review-text">{{
                            review.text || 'Пользователь оставил только оценку, без текста отзыва.'
                        }}</p>
                </div>
            </div>

            <div v-if="totalPages > 1" class="pagination">
                <button
                    :disabled="currentPage === 1 || isLoading"
                    @click="changePage(currentPage - 1)"
                    class="page-btn">
                    Назад
                </button>

                <span class="page-info">Страница {{ currentPage }} из {{ totalPages }}</span>

                <button :disabled="currentPage === totalPages || isLoading"
                        @click="changePage(currentPage + 1)"
                        class="page-btn">
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

        const response = await axios.post('/companies', {
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

            const response = await axios.post('/companies', { url: url.value });

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
<style scoped>
/*.settings-page {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    width: 100%;
    padding: 2rem 1rem;
    background-color: #f8fafc;
    box-sizing: border-box;
}

.container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

.settings-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    box-sizing: border-box;
}

.data-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    width: 100%;
    box-sizing: border-box;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    width: 100%;
}

.review-item {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    border: 1px solid #e2e8f0;
}

h2 {
    margin-bottom: 0.5rem;
    color: #1a202c;
}

.subtitle {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: bold;
    font-size: 0.85rem;
    color: #4a5568;
}

input {
    padding: 0.75rem;
    border: 1px solid #cbd5e0;
    border-radius: 4px;
    font-size: 1rem;
    width: 100%;
}

input:focus {
    border-color: #3182ce;
    outline: none;
}

button {
    width: 100%;
    padding: 0.75rem;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: bold;
    margin-bottom: 1rem;
}

button:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
}

.error-alert {
    background: #fff5f5;
    color: #c53030;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #fed7d7;
    font-size: 0.9rem;
    text-align: center;
}

.success-alert {
    background: #f0fff4;
    color: #22543d;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #c6f6d5;
    font-size: 0.9rem;
    text-align: center;
}

h2, h3 {
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.inline-form input {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid #cbd5e0;
    border-radius: 4px;
    font-size: 1rem;
}

.inline-form input {
    border-color: #e53e3e;
    background-color: #fff5f5;
}

.inline-form button {
    padding: 0.75rem 1.5rem;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s;
}

.inline-form button:hover {
    background: #2b6cb0;
}

.error-alert {
    background: #fff5f5;
    color: #c53030;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #fed7d7;
    font-size: 0.9rem;
    text-align: center;
    font-weight: 500;
}

.success-alert {
    background: #f0fff4;
    color: #22543d;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #c6f6d5;
    font-size: 0.9rem;
    text-align: center;
    line-height: 1.4;
}

.company-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #edf2f7;
    padding-bottom: 1rem;
}

.stats-grid {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-box {
    flex: 1;
    background: #f7fafc;
    padding: 1.25rem;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
}

.stat-box.rating {
    background: #fffaf0;
    border-color: #feebc8;
}

.stat-val {
    font-size: 1.6rem;
    font-weight: bold;
    color: #2d3748;
}

.stat-box.rating .stat-val {
    color: #dd6b20;
}

.stat-lbl {
    font-size: 0.8rem;
    color: #718096;
    margin-top: 0.25rem;
    text-align: center;
    font-weight: 500;
}

.review-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.review-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

.author {
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 600;
}

.stars {
    background-color: #fef3c7;
    color: #d97706;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.date {
    color: #94a3b8;
    font-size: 0.875rem;
    margin-left: auto;
}

.review-text {
    color: #475569;
    line-height: 1.6;
    margin: 0;
    font-size: 1rem;
    white-space: pre-line;
}

.review-item p.review-text:only-child,
.review-text {
    color: #94a3b8;
    font-style: italic;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 2rem auto;
    max-width: 800px;
}

.page-btn {
    width: auto;
    margin-bottom: 0;
    padding: 0.5rem 1rem;
    background: #ffffff;
    color: #4a5568;
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
    background: #f7fafc;
    border-color: #a0aec0;
    color: #1a202c;
}

.page-btn:disabled {
    background: #edf2f7;
    color: #a0aec0;
    cursor: not-allowed;
}

.page-info {
    font-size: 0.9rem;
    color: #4a5568;
    font-weight: 500;
}*/
</style>
