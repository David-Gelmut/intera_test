import { defineStore } from 'pinia';
import axios from 'axios';

export const useChatStore = defineStore('chat', {
    state: () => ({
        chatList: [],
        usersList: [],
        activeChatId: null,
        activeInterlocutor: null,
        messages: []
    }),

    actions: {
        async fetchChats() {
            try {
                const response = await axios.get('/api/chats');
                this.chatList = response.data;
            } catch (error) {
                console.error('Ошибка загрузки чатов:', error);
            }
        },

        async fetchUsersList() {
            try {
                const response = await axios.get('/api/chats/users');
                this.usersList = response.data;
            } catch (error) {
                console.error('Ошибка загрузки контактов:', error);
            }
        },

        // Загрузка истории сообщений из базы данных
        async loadMessages(chatId) {
            try {
                const response = await axios.get(`/api/chats/${chatId}/messages`);
                this.messages = response.data;
            } catch (error) {
                console.error('Ошибка загрузки сообщений:', error);
            }
        },

        // Отправка нового сообщения на бэкенд
        async sendMessageAction(chatId, text) {
            try {
                const response = await axios.post(`/api/chats/${chatId}/messages`, { text });
                this.messages.push(response.data); // Сразу пушим своё сообщение в ленту
                return response.data;
            } catch (error) {
                console.error('Ошибка отправки сообщения:', error);
                throw error;
            }
        },

        //Метод пометки чата прочитанным
        async readChatMessages(chatId) {
            try {
                await axios.post(`/api/chats/${chatId}/read`);
                this.resetUnreadCount(chatId);
            } catch (error) {
                console.error('Ошибка прочтения чата:', error);
            }
        },

        // Очистить историю сообщений
        async clearChatAction(chatId) {
            try {
                await axios.post(`/api/chats/${chatId}/clear`);
                this.messages = []; // Мгновенно очищаем ленту сообщений на экране
            } catch (error) {
                console.error('Ошибка очистки чата:', error);
                throw error;
            }
        },

        // Полностью удалить чат для всех участников
        async deleteChatAction(chatId) {
            try {
                await axios.delete(`/api/chats/${chatId}`);

                // Сбрасываем текущее активное состояние
                this.activeChatId = null;
                this.activeInterlocutor = null;
                this.messages = [];

                // Обновляем список чатов
                await this.fetchChats();
            } catch (error) {
                console.error('Ошибка удаления чата:', error);
                throw error;
            }
        },

        incrementUnreadCount(chatId) {
            const chat = this.chatList.find(c => c.id === chatId);
            if (chat) {
                chat.unread_count = (chat.unread_count || 0) + 1;
            } else {
                this.fetchChats();
            }
        },

        resetUnreadCount(chatId) {
            const chat = this.chatList.find(c => c.id === chatId);
            if (chat) chat.unread_count = 0;
        }
    }
});
