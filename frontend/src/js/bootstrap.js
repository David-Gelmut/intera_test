import axios from "axios";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            const status = error.response.status;
            const message = error.response.data?.message || '';

            // СИТУАЦИЯ 1: Сессия реально протухла на сервере (Laravel вернул 401)
            if (status === 401) {
                localStorage.removeItem('user');
                window.location.href = '/login';
                return Promise.reject(error);
            }

            // СИТУАЦИЯ 2: Доступ запрещен (Laravel вернул 403)
            if (status === 403) {
                // Проверяем, забанен ли пользователь (смотрим на текст ошибки с бэка)
                if (message.includes('блокирован') || message.includes('неактивен')) {
                    localStorage.removeItem('user');
                    window.location.href = '/banned';
                } else {
                    // Если это просто чужой чат или закрытый роут — НЕ вылогиниваем!
                    // Просто выводим ошибку на экран, пользователь остается на сайте
                    alert(message || 'Доступ к этому ресурсу ограничен.');
                }
                return Promise.reject(error);
            }
        }
        return Promise.reject(error);
    }
);

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post(`/api/broadcasting/auth`, {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                    .then(response => {
                        callback(false, response.data);
                    })
                    .catch(error => {
                        callback(true, error);
                    });
            }
        };
    },
});

