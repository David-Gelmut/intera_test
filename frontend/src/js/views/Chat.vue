<template>
  <div
      class=" flex h-[calc(100vh-theme(spacing.16)-theme(spacing.1))] border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs font-sans">

    <!-- Левая колонка: Переключатель Чаты / Контакты -->
    <!--div class=" w-80 border-r border-slate-200 flex flex-col bg-slate-50/50"-->
    <div class="w-full md:w-80 pt-5 absolute md:static inset-0 z-10 border-r border-slate-200 flex flex-col bg-slate-50/50"
         :class="chatStore.activeChatId ? 'hidden md:flex' : 'flex'">
      <!-- Вкладки управления -->
      <div class="p-4 border-b border-slate-200 bg-white space-y-3">
        <h2 class="text-lg font-bold text-slate-900">Мессенджер</h2>

        <!-- Кнопки переключения режимов -->
        <div class="flex rounded-lg bg-slate-100 p-0.5 border border-slate-200/50">
          <button
              @click="activeTab = 'chats'"
              class="flex-1 text-xs py-1.5 font-medium rounded-md transition-all cursor-pointer"
              :class="activeTab === 'chats' ? 'bg-white text-indigo-600 shadow-xs font-semibold' : 'text-slate-500 hover:text-slate-800'"
          >
            Диалоги
          </button>
          <button
              @click="activeTab = 'contacts'"
              class="flex-1 text-xs py-1.5 font-medium rounded-md transition-all cursor-pointer"
              :class="activeTab === 'contacts' ? 'bg-white text-indigo-600 shadow-xs font-semibold' : 'text-slate-500 hover:text-slate-800'"
          >
            Контакты
          </button>
        </div>
      </div>

      <!-- СПИСОК 1: Существующие диалоги (активные чаты) -->
      <div v-if="activeTab === 'chats'" class="flex-1 overflow-y-auto divide-y divide-slate-100 bg-white">
        <div v-if="chatStore.chatList.length === 0" class="p-8 text-center text-xs text-slate-400">
          У вас пока нет активных диалогов. Перейдите во вкладку "Контакты", чтобы начать общение.
        </div>

        <button
            v-for="chat in chatStore.chatList"
            :key="chat.id"
            @click="selectChat(chat.id, chat.users[0])"
            class="w-full text-left p-4 flex items-center gap-3 transition-colors cursor-pointer group"
            :class="chatStore.activeChatId === chat.id ? 'bg-indigo-50/70 border-l-4 border-indigo-600 pl-3' : 'hover:bg-slate-50'"
        >

          <!-- Круглая иконка пользователя (Аватарка с инициалом) -->
          <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-sm text-indigo-700 uppercase tracking-wider group-hover:bg-indigo-200 transition-colors">
            {{ chat.users[0] ? chat.users[0].name.charAt(0) : 'U' }}
          </div>

          <!-- Имя собеседника и превью сообщения -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <span class="font-semibold text-sm text-slate-800 truncate">
                {{ chat.users[0] ? chat.users[0].name : 'Собеседник' }}
              </span>
              <!-- КРУЖОК НЕПРОЧИТАННЫХ СООБЩЕНИЙ -->
              <span v-if="chat.unread_count > 0"
                    class="flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-500 px-1 text-[10px] font-bold text-white shadow-xs">
                {{ chat.unread_count }}
              </span>
            </div>
            <p class="text-xs text-slate-500 truncate mt-0.5">Нажмите, чтобы открыть переписку...</p>
          </div>

        </button>
      </div>

      <!-- СПИСОК 2: Все пользователи системы (Контакты) -->
      <div v-if="activeTab === 'contacts'" class="flex-1 overflow-y-auto divide-y divide-slate-100 bg-white">
        <div v-if="chatStore.usersList.length === 0" class="p-8 text-center text-xs text-slate-400">
          Загрузка списка пользователей или контакты отсутствуют...
        </div>
        <button
            v-for="user in chatStore.usersList"
            :key="user.id"
            @click="createOrGetChat(user)"
            class="w-full text-left p-4 flex items-center justify-between hover:bg-slate-50 transition-colors cursor-pointer"
        >
          <div class="flex flex-col min-w-0">
            <span class="font-semibold text-sm text-slate-800 truncate">{{ user.name }}</span>
            <span class="text-xs text-slate-400 truncate">{{ user.email }}</span>
          </div>
          <!-- Метка роли -->
          <span
              class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-sm bg-slate-100 text-slate-500">
            {{ user.role }}
          </span>
        </button>
      </div>

    </div>


    <!-- Правая колонка: Окно переписки -->
    <!--div class="flex-1 flex flex-col bg-slate-50/30"-->
    <div class="flex-1 flex flex-col bg-slate-50/30" :class="chatStore.activeChatId ? 'flex' : 'hidden md:flex'">

      <!-- Если чат выбран -->
      <template v-if="chatStore.activeChatId">

        <!-- Шапка чата -->
        <div class="h-14 border-b border-slate-200 bg-white px-6 flex items-center justify-between shadow-2xs">

          <button @click="chatStore.activeChatId = null" class="md:hidden mr-2 p-1 text-slate-500 hover:text-slate-700 cursor-pointer">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
          </button>

          <div class="flex items-center gap-3">
            <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-xs text-slate-600 uppercase">
              {{ chatStore.activeInterlocutor ? chatStore.activeInterlocutor.name.charAt(0) : 'U' }}
            </div>
            <div class="flex flex-col">
              <span class="font-bold text-sm text-slate-800">{{
                  chatStore.activeInterlocutor ? chatStore.activeInterlocutor.name : 'Чат'
                }}</span>
<!--              <span class="text-[11px] text-emerald-600 font-medium">Онлайн-трансляция сообщений</span>-->
            </div>
          </div>



          <!-- КНОПКИ УПРАВЛЕНИЯ ЧАТОМ -->
          <div class="flex items-center gap-2">
            <button
                @click="clearChat"
                title="Очистить диалог"
                class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer text-xs font-medium"
            >
              Очистить
            </button>
            <button
                @click="deleteChat"
                title="Удалить чат"
                class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer text-xs font-medium"
            >
              Удалить
            </button>
          </div>
        </div>


        <!-- Лента сообщений -->
        <div ref="messageContainer" class="flex-1 p-6 overflow-y-auto space-y-4">
          <div
              v-for="msg in chatStore.messages"
              :key="msg.id"
              class="flex flex-col max-w-[70%]"
              :class="isMyMessage(msg.user_id) ? 'ml-auto items-end' : 'items-start'"
          >
            <!-- Имя отправителя (только для чужих сообщений) -->
            <span v-if="!isMyMessage(msg.user_id)" class="text-xs text-slate-400 font-medium ml-2 mb-1">
              {{ msg.user?.name }}
            </span>

            <!-- Облачко -->
            <div
                class="px-4 py-2.5 rounded-2xl text-sm shadow-2xs leading-relaxed"
                :class="isMyMessage(msg.user_id)
                ? 'bg-indigo-600 text-white rounded-br-none'
                : 'bg-white text-slate-800 border border-slate-100 rounded-bl-none'"
            >
              {{ msg.text }}
            </div>

          </div>
        </div>

        <!-- Подвал чата: Ввод текста -->
        <div class="p-4 border-t border-slate-200 bg-white shadow-2xs">
          <form @submit.prevent="sendMessage" class="flex items-center gap-3">
            <input
                v-model="newMessageText"
                type="text"
                placeholder="Напишите сообщение..."
                class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-shadow"
            />
            <button
                type="submit"
                :disabled="!newMessageText.trim()"
                class="bg-indigo-600 text-white p-2.5 rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:pointer-events-none transition-colors cursor-pointer"
            >
              <svg class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                   stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </form>
        </div>

      </template>

      <!-- Если чат НЕ выбран -->
      <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
        <svg class="h-16 w-16 text-slate-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <h3 class="mt-4 font-bold text-slate-700 text-base">Ваш мессенджер готов</h3>
        <p class="text-xs text-slate-400 max-w-xs mt-1">Выберите нужный диалог в левой колонке панели, чтобы начать
          переписку в реальном времени.</p>
      </div>

    </div>

  </div>
</template>

<script setup>
import {ref, onMounted, onUnmounted, nextTick} from 'vue';
import {useAuthStore} from '../store/auth.js';
import {useChatStore} from '../store/chat.js';

const authStore = useAuthStore();
const chatStore = useChatStore();

const activeTab = ref('chats');
const newMessageText = ref('');
const messageContainer = ref(null);

let currentUserId = authStore.user?.id || null;
const isMyMessage = (id) => id === currentUserId;

onMounted(() => {
  chatStore.fetchChats();
  chatStore.fetchUsersList();
});

onUnmounted(() => {
  if (chatStore.activeChatId) {
    window.Echo.leaveChannel(`chat.${chatStore.activeChatId}`);
  }
});

// Клик по контакту
const createOrGetChat = async (targetUser) => {
  try {
    const response = await axios.post('/api/chats', {user_id: targetUser.id});
    const chatId = response.data.chat_id;

    await chatStore.fetchChats();
    activeTab.value = 'chats';

    selectChat(chatId, targetUser);
  } catch (error) {
    alert('Не удалось запустить чат');
  }
};

// Выбор и открытие чата
const selectChat = async (id, user = null) => {

  if (chatStore.activeChatId) {
    window.Echo.leaveChannel(`chat.${chatStore.activeChatId}`);
  }

  chatStore.activeChatId = id;
  chatStore.activeInterlocutor = user;

  await chatStore.loadMessages(id);
  await chatStore.readChatMessages(id);
  scrollToBottom();

  window.Echo.private(`chat.${id}`)
      .listen('MessageSent', (e) => {
        chatStore.messages.push(e);
        // console.log( chatStore.messages);
        scrollToBottom();
        chatStore.readChatMessages(id);
      });
};

// Отправка сообщения
const sendMessage = async () => {
  if (!newMessageText.value.trim()) return;

  const textToSend = newMessageText.value;
  newMessageText.value = '';

  await chatStore.sendMessageAction(chatStore.activeChatId, textToSend);
  scrollToBottom();
};

// Очистить сообщения
const clearChat = async () => {
  if (!confirm('Вы уверены, что хотите очистить историю?')) return;
  await chatStore.clearChatAction(chatStore.activeChatId);
};

// Полностью удалить чат
const deleteChat = async () => {
  if (!confirm('Вы уверены, что хотите безвозвратно удалить чат?')) return;
  window.Echo.leaveChannel(`chat.${chatStore.activeChatId}`);
  await chatStore.deleteChatAction(chatStore.activeChatId);
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
  });
};
</script>

