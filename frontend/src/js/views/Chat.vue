<template>
  <div
      class=" flex h-[calc(100vh-theme(spacing.16)-theme(spacing.1))] border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs font-sans">

    <!-- Левая колонка: Переключатель Чаты / Контакты -->
    <div
        class="w-full md:w-80 pt-5 absolute md:static inset-0 z-10 border-r border-slate-200 flex flex-col bg-slate-50/50"
        :class="chatStore.activeChatId ? 'hidden md:flex' : 'flex'">
      <!-- Вкладки управления -->
      <div class="p-4 border-b border-slate-200 bg-white space-y-3">
<!--        <h2 class="text-lg font-bold text-slate-900">Мессенджер</h2>-->

<!--        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-700 text-white shadow-md shrink-0">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
          </div>

          <div class="flex flex-col min-w-0">
            <h2 class="text-base font-black text-slate-900 tracking-tight leading-none uppercase">City Of Masters</h2>
            <span class="text-[10px] text-indigo-600 font-bold tracking-wider uppercase mt-1">Professional Network</span>
          </div>
        </div>-->

<!--        <div class="flex items-center gap-2.5">
          &lt;!&ndash; Иконка "City Of Masters" &ndash;&gt;
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-2xs shrink-0">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z" />
              <path d="M15 9h-2v2h2V9zm-4 0H9v2h2V9z" />
              <path d="M9 14h6" />
            </svg>
          </div>

          <div class="flex flex-col min-w-0">
            <h2 class="text-sm font-bold text-slate-900 tracking-tight leading-none uppercase">City Of Masters</h2>
            <span class="text-[10px] text-slate-400 font-medium mt-1">Платформа гильдии мастеров</span>
          </div>
        </div>-->


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
    <div class="flex-1 flex flex-col bg-slate-50/30 max-w-full" :class="chatStore.activeChatId ? 'flex' : 'hidden md:flex'">

      <!-- Если чат выбран -->
      <template v-if="chatStore.activeChatId">

        <!-- Шапка чата -->
        <div class="h-14 border-b border-slate-200 bg-white px-6 flex items-center justify-between shadow-2xs">

          <button @click="chatStore.activeChatId = null"
                  class="md:hidden mr-2 p-1 text-slate-500 hover:text-slate-700 cursor-pointer">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
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
        <div ref="messageContainer" class="flex-1 p-4 md:p-6 overflow-y-auto space-y-4">
          <div
              v-for="msg in chatStore.messages"
              :key="msg.id"
              class="flex flex-col max-w-[85%] md:max-w-[70%]"
              :class="isMyMessage(msg.user_id) ? 'ml-auto items-end' : 'items-start'"
          >
            <!-- Имя отправителя (только для чужих сообщений) -->
            <span v-if="!isMyMessage(msg.user_id)" class="text-xs text-slate-400 font-medium ml-2 mb-1">
            {{ msg.user?.name }}
          </span>

            <!-- Облачко со смешанным контентом (Текст + Файлы) -->
            <div
                class="px-4 py-2.5 rounded-2xl text-sm shadow-2xs leading-relaxed break-words"
                :class="isMyMessage(msg.user_id)? 'bg-indigo-600 text-white rounded-br-none': 'bg-white text-slate-800 border border-slate-100 rounded-bl-none'"
            >
              <!-- Вывод текста сообщения (если он есть) -->
              <span v-if="msg.text" class="block mb-1 whitespace-pre-wrap">{{ msg.text }}</span>

              <!-- Вывод прикрепленных файлов (если они есть в attachments) -->
              <div v-if="msg.attachments && msg.attachments.length > 0" v-viewer class="space-y-2 mt-2">
                <div v-for="file in msg.attachments" :key="file.id">

                  <!-- Если файл — КАРТИНКА -->
                  <div v-if="file.file_type === 'image'" class="max-w-xs">
                    <!-- УБРАЛИ @click, плагин сам обработает нажатие -->
                    <img
                        :src="getFileUrl(file.file_path)"
                        alt="Изображение"
                        class="rounded-lg max-h-60 w-full object-cover cursor-pointer hover:opacity-90 transition-opacity border border-slate-100/50"
                    />
                  </div>

                  <!-- Если файл — ВИДЕО -->
                  <div v-else-if="file.file_type === 'video'" class="max-w-xs sm:max-w-sm my-1 relative group">
                    <video
                        :src="getFileUrl(file.file_path)"
                        controls
                        playsinline
                        preload="metadata"
                        class="rounded-xl max-h-64 w-full object-contain bg-black border border-slate-100/10 shadow-sm"
                    >
                      Ваш браузер не поддерживает воспроизведение видео.
                    </video>

                    <!-- Кнопка развертывания, которая появляется при наведении на видео -->
                    <button
                        type="button"
                        @click="openVideoModal(getFileUrl(file.file_path))"
                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-black/60 text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer hover:bg-black/80"
                        title="Развернуть на весь экран"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0l-6-6" />
                      </svg>
                    </button>

                  </div>


                  <!-- Если файл — ДОКУМЕНТ -->
                  <div v-else class="flex items-center gap-2 py-1.5 px-2 rounded-xl bg-black/5 max-w-xs">
                    <svg class="h-7 w-7 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <div class="flex flex-col min-w-0 flex-1">
                      <span class="font-medium text-xs truncate" :class="isMyMessage(msg.user_id) ? 'text-white' : 'text-slate-800'">
                        {{ file.file_name }}
                      </span>
                      <a
                          target="_blank"
                          download="true"
                          :href="getFileUrl(file.file_path)"
                          class="text-[10px] underline font-semibold mt-0.5 text-left cursor-pointer transition-colors"
                          :class="isMyMessage(msg.user_id) ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800'"
                      >
                        Скачать
                      </a>
                    </div>
                  </div>

                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- Подвал чата: Ввод текста и прикрепление файлов -->
        <div class="p-4 border-t border-slate-200 bg-white shadow-2xs relative">

          <!-- Окошко выбора Эмодзи -->
          <div v-if="showEmojiPicker" class="absolute bottom-20 right-4 z-50 shadow-xl rounded-xl overflow-hidden">
            <EmojiPicker :native="true" @select="onSelectEmoji" />
          </div>

          <!-- Скрытый системный инпут для файлов -->
          <input
              type="file"
              ref="fileInput"
              class="hidden"
              multiple
              @change="onFilesSelected"
          />

          <form @submit.prevent="sendMessage" class="flex flex-col gap-2">

            <!-- БЛОК ПРЕДПРОСМОТРА: Показывается только если прикреплены файлы -->
            <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2 p-2 bg-slate-50 border border-slate-100 rounded-xl max-h-32 overflow-y-auto">
              <div
                  v-for="(file, index) in selectedFiles"
                  :key="index"
                  class="flex items-center gap-1.5 pl-2 pr-1 py-1 rounded-lg bg-white border border-slate-200 text-xs text-slate-700 max-w-[200px]"
              >
                <!-- Иконка скрепки для визуального обозначения файла -->
                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>

                <span class="truncate flex-1 font-medium">{{ file.name }}</span>

                <!-- Кнопка удаления файла из списка (крестик) -->
                <button
                    type="button"
                    @click="removeFile(index)"
                    class="p-0.5 text-slate-400 hover:text-rose-500 rounded-md hover:bg-slate-100 cursor-pointer"
                    title="Убрать файл"
                >
                  <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Основная строка управления (Кнопки и Инпут) -->
            <div class="flex items-center gap-2 md:gap-3">

              <!-- Кнопка «Скрепка» -->
              <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer shrink-0"
                  title="Прикрепить файлы"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
              </button>

              <!-- Кнопка «Смайлик» -->
              <button
                  type="button"
                  @click="showEmojiPicker = !showEmojiPicker"
                  class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer shrink-0"
                  title="Добавить эмодзи"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>

              <!-- Поле ввода текста -->
              <input
                  v-model="newMessageText"
                  type="text"
                  placeholder="Напишите сообщение..."
                  class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-shadow min-w-0"
              />

              <!-- Кнопка отправки «Самолетик» (Разблокируется, если есть текст ИЛИ прикреплен файл) -->
              <button
                  type="submit"
                  :disabled="!newMessageText.trim() && selectedFiles.length === 0"
                  class="bg-indigo-600 text-white p-2.5 rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:pointer-events-none transition-colors cursor-pointer shrink-0"
                  title="Отправить сообщение"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3 21l19-9L3 3l3 9m0 0h6" />
                </svg>
              </button>

            </div>
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

  <!-- МОДАЛЬНОЕ ОКНО ДЛЯ ПРОСМОТРА ВИДЕО НА ВЕСЬ ЭКРАН -->
  <div
      v-if="isVideoModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 animate-fade-in"
      @click.self="closeVideoModal"
  >
    <!-- Кнопка закрытия (крестик) -->
    <button
        type="button"
        @click="closeVideoModal"
        class="absolute top-4 right-4 p-2 text-white/70 hover:text-white rounded-full bg-white/10 hover:bg-white/20 transition-colors cursor-pointer"
    >
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Контейнер с видеоплеером -->
    <div class="max-w-4xl w-full max-h-[85vh] flex items-center justify-center">
      <video
          :src="currentModalVideoUrl"
          controls
          autoplay
          playsinline
          class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain bg-black"
      >
        Ваш браузер не поддерживает воспроизведение видео.
      </video>
    </div>
  </div>



</template>

<script setup>
import {ref, onMounted, onUnmounted, nextTick, watch} from 'vue';
import {useAuthStore} from '../store/auth.js';
import {useChatStore} from '../store/chat.js';
import EmojiPicker from 'vue3-emoji-picker';

const authStore = useAuthStore();
const chatStore = useChatStore();

const activeTab = ref('chats');
const newMessageText = ref('');
const messageContainer = ref(null);
const showEmojiPicker = ref(false);
const fileInput = ref(null);
const selectedFiles = ref([]);
const isVideoModalOpen = ref(false);
const currentModalVideoUrl = ref('');
let currentUserId = authStore.user?.id || null;

const isMyMessage = (id) => id === currentUserId;
const BACKEND_URL = axios.defaults.baseURL;

// Функция для открытия видео на весь экран
function openVideoModal(url) {
  currentModalVideoUrl.value = url;
  isVideoModalOpen.value = true;
}

// Функция для закрытия модалки
function closeVideoModal() {
  isVideoModalOpen.value = false;
  currentModalVideoUrl.value = '';
}

// Функция для склеивания путей
function getFileUrl(path) {
  if (!path) return '';
  // Если бэкенд уже прислал полную ссылку (с http), возвращаем её
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }
  // Убираем лишние слэши при склеивании, если они есть
  const cleanPath = path.startsWith('/') ? path : `/${path}`;
  return `${BACKEND_URL}${cleanPath}`;
}

// Функция, которая вызывается при клике на эмодзи
function onSelectEmoji(emoji) {
  // Добавляем выбранный эмодзи в текущую позицию текста
  newMessageText.value += emoji.i;
  // Закрываем пикер после выбора (по желанию)
  showEmojiPicker.value = false;
}

onMounted(() => {
  chatStore.fetchChats();
  chatStore.fetchUsersList();
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

        /* if (!chatStore.messages.some(m => m.id === e.id)) {
           chatStore.messages.push(e.message);
         }*/
        chatStore.messages.push(e);
        scrollToBottom();
        chatStore.readChatMessages(id);
      });
};

// Отправка сообщения
const sendMessage = async () => {

  if (!newMessageText.value.trim() && selectedFiles.value.length === 0) return;

  const formData = new FormData();
  formData.append('chat_id', chatStore.activeChatId);

  if (newMessageText.value.trim()) {
    formData.append('text', newMessageText.value);
  }

  if (selectedFiles.value.length > 0) {
    selectedFiles.value.forEach(file => {
      formData.append('files[]', file);
    });
  }

  const message = await chatStore.sendMessageAction(chatStore.activeChatId, formData);

  if (!chatStore.messages.some(m => m.id === message.id)) {
    chatStore.messages.push(message);
  }

  newMessageText.value = '';
  selectedFiles.value = [];
  if (fileInput.value) fileInput.value.value = '';

  scrollToBottom();
};


// Хэндлер просто собирает файлы в массив для предпросмотра
function onFilesSelected(event) {
  const files = event.target.files;
  if (files.length > 0) {
    // Добавляем новые файлы к уже выбранным (чтобы можно было прикреплять несколько раз)
    selectedFiles.value.push(...Array.from(files));
  }
}

// Функция для удаления файла из списка перед отправкой, если пользователь передумал
function removeFile(index) {
  selectedFiles.value.splice(index, 1);
}


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


// Главная функция скролла
function scrollToBottom() {
  // nextTick ждет, пока Vue обновит HTML-дерево и вставит новые облачка в DOM
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTo({
        top: messageContainer.value.scrollHeight,
        behavior: 'smooth' // Плавный красивый скролл
      });
    }
  });
}

// Скроллим вниз, когда в массив chatStore.messages добавляются новые сообщения
watch(
    () => chatStore.messages,
    () => {
      scrollToBottom();
    },
    { deep: true } // deep нужен, так как мы следим за изменениями внутри массива
);



//ЛОГИКА ДЛЯ СИТУАЦИИ КОГДА СОКЕТЫ ОТВАЛИЛИСЬ
// Переменная для хранения таймера опроса БД
let fallbackPollingInterval = null;

// Слушаем встроенные статусы подключения Pusher/Reverb
window.Echo.connector.pusher.connection.bind('state_change', (states) => {
  /*
    Доступные статусы:
    'connecting' (подключение), 'connected' (подключено),
    'unavailable' (недоступен), 'failed' (ошибка), 'disconnected' (отключен)
  */
  console.log('Статус сокета изменился:', states.current);

  if (states.current === 'connected') {
    // 1. Если сокет ожил или переподключился — выключаем аварийный опрос БД
    if (fallbackPollingInterval) {
      clearInterval(fallbackPollingInterval);
      fallbackPollingInterval = null;
    }
    // Дозагружаем сообщения, которые могли пропустить, пока сокет лежал
    syncMissedMessages();

  } else if (states.current === 'unavailable' || states.current === 'disconnected') {
    // 2. Если контейнер сокетов упал — включаем аварийный режим опроса БД
    if (!fallbackPollingInterval) {
      startFallbackPolling();
    }
  }
});

// Функция аварийного опроса (работает, пока лежит контейнер сокетов)
function startFallbackPolling() {
  console.warn('Сокеты недоступны. Включен режим аварийного опроса БД...');

  fallbackPollingInterval = setInterval(() => {
    // Опрашиваем базу, только если у нас открыт какой-то конкретный чат
    if (chatStore.activeChatId) {
      syncMissedMessages();
    }
  }, 5000); // Опрос каждые 5 секунд
}

// Функция, которая забирает из БД новые сообщения
async function syncMissedMessages() {
  if (!chatStore.messages.length || !chatStore.activeChatId) return;

  // Берем ID самого последнего сообщения, которое сейчас отображено на экране
  const lastMessageId = chatStore.messages[chatStore.messages.length - 1].id;

  try {
    // Делаем обычный HTTP-запрос к бэкенду
    const response = await axios.get(`/api/chats/${chatStore.activeChatId}/sync?last_id=${lastMessageId}`);
    //TODO  проверить условие !chatStore.messages.some(m => m.id === msg.id)
    if (response.data && response.data.length > 0) {
      response.data.forEach(msg => {

        //if (!chatStore.messages.some(m => m.id === msg.id)) {
          chatStore.messages.push(msg);
        //  }
      });
    }
  } catch (error) {
    console.error('Не удалось синхронизировать сообщения с БД:', error);
  }
}

onUnmounted(() => {

  if (chatStore.activeChatId) {
    window.Echo.leaveChannel(`chat.${chatStore.activeChatId}`);
    chatStore.activeChatId = null;
  }
  console.log('Пользователь покинул чат. Полная очистка ресурсов...');
});


</script>
<style>

</style>
