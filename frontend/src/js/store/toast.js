import { defineStore } from 'pinia';

export const useToastStore = defineStore('toast', {
    state: () => ({
        toasts: [] // Массив активных уведомлений на экране
    }),
    actions: {
        /**
         * Показать всплывающее уведомление
         * @param {string} title - Заголовок (например, имя отправителя)
         * @param {string} message - Текст сообщения
         * @param {string} type - Тип: 'info', 'success', 'error'
         */
        add(title, message, type = 'info') {
            const id = Date.now() + Math.random(); // Уникальный ID для v-for

            // Добавляем новое уведомление в начало массива
            this.toasts.unshift({ id, title, message, type });

            // Автоматически удаляем плашку
            setTimeout(() => {
                this.remove(id);
            }, 10000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }
});
