import axios from "axios";

window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';

axios.interceptors.response.use(
    response => response, // Если всё хорошо, просто пропускаем ответ
    error => {
        // Сессия умерла
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        // Аккаунт заблокирован администратором
        if (error.response.status === 403) {
            localStorage.removeItem('user'); // Очищаем данные фронта
            window.location.href = '/banned'; // Отправляем на страницу блокировки
        }

        return Promise.reject(error);
    }
);
