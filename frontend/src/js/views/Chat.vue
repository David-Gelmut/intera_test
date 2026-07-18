<template>
  <div class="space-y-6">
    <div class="border-b border-slate-200 pb-5">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Сообщения (Real-time мессенджер)</h1>
      <p>Коммуникационное ядро платформы, обеспечивающее мгновенную и безопасную связь между всеми участниками экосистемы.</p>
      <p></p>
      <ul class="mt-1 text-sm text-slate-500">
        <li>Зачем нужна: Для оперативного обсуждения деталей заказов, согласования этапов проектирования, пересылки рабочих документов и поддержания связи внутри проектных команд.</li>
        <li>Формат контента: Диалоги и групповые чаты, поддерживающие отправку зашифрованного текста со смайликами, встроенное воспроизведение видеороликов, галереи изображений и скачивание документов любых форматов.</li>
        <li>Логика работы: Работает на базе веб-сокетов Reverb и очередей RabbitMQ.</li>
      </ul>
    </div>

    <div class="max-w-3xl">

    </div>

  </div>
  <div
      class=" flex h-[calc(100vh-theme(spacing.16)-theme(spacing.1))] border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs font-sans">

    <!-- Левая колонка: Переключатель Чаты / Контакты -->
    <div
        class="w-full md:w-80 pt-5 absolute md:static inset-0 z-10 border-r border-slate-200 flex flex-col bg-slate-50/50"
        :class="chatStore.activeChatId ? 'hidden md:flex' : 'flex'">
      <!-- Вкладки управления -->
      <div class="p-4 border-b border-slate-200 bg-white space-y-3">

        <!-- Кнопки переключения режимов -->
        <div class="flex md:mt-0 mt-16 rounded-lg bg-slate-100 p-0.5 border border-slate-200/50">
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

        <div
            v-for="chat in chatStore.chatList"
            :key="chat.id"
            @click="selectChat(chat.id, chat.users[0])"
            class="w-full text-left p-4 flex items-center gap-3 transition-colors cursor-pointer group"
            :class="chatStore.activeChatId === chat.id ? 'bg-indigo-50/70 border-l-4 border-indigo-600 pl-3' : 'hover:bg-slate-50'"
        >

          <!-- Круглая иконка пользователя (Аватарка с инициалом) -->
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-sm text-indigo-700 uppercase tracking-wider group-hover:bg-indigo-200 transition-colors overflow-hidden border border-indigo-200/50">

            <!-- Добавили класс rounded-full на саму картинку -->
            <img
                v-if="chat.users[0]?.avatar_path"
                :src="chat.users[0].avatar_path"
                alt="Аватар"
                class="h-full w-full object-cover rounded-full"
            />

            <template v-else>
              {{ chat.users[0].name ? chat.users[0].name.charAt(0) : 'U' }}
            </template>

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

            <!-- Динамическое превью последнего сообщения (Как в Telegram) -->
            <p class="text-xs truncate mt-0.5" :class="chat.unread_count > 0 ? 'text-slate-900 font-semibold' : 'text-slate-500'">
              <template v-if="chat.last_message">

                <!-- Если в сообщении есть прикрепленные файлы -->
                <template v-if="chat.last_message.attachments && chat.last_message.attachments.length > 0">
                  <span class="text-blue-600 font-medium">
                    <template v-if="chat.last_message.attachments[0].file_type === 'image'">📷 Фотография</template>
                    <template v-if="chat.last_message.attachments[0].file_type === 'video'">🎥 Видеоролик</template>
                    <template v-if="chat.last_message.attachments[0].file_type === 'file'">📎 Файл: {{ chat.last_message.attachments[0].file_name }}</template>
                  </span>
                  <span v-if="chat.last_message.text" class="text-slate-400 ml-1">— {{ chat.last_message.text }}</span>
                </template>

                <!-- Если отправлен только чистый текст -->
                <template v-else>
                  {{ chat.last_message.text }}
                </template>

              </template>
              <template v-else>
                <span class="text-slate-400 italic">Нет сообщений...</span>
              </template>
            </p>

<!--            <p class="text-xs text-slate-500 truncate mt-0.5">Нажмите, чтобы открыть переписку...</p>-->
          </div>

        </div>

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


          <div class="flex flex-row gap-2 min-w-0">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-sm text-indigo-700 uppercase tracking-wider group-hover:bg-indigo-200 transition-colors overflow-hidden border border-indigo-200/50">
              <!-- Добавили класс rounded-full на саму картинку -->
              <img
                  v-if="user.avatar_path"
                  :src="user.avatar_path"
                  alt="Аватар"
                  class="h-full w-full object-cover rounded-full"
              />
              <template v-else>
                {{ authStore.user?.name ? authStore.user.name.charAt(0) : 'U' }}
              </template>
            </div>
            <div class="flex flex-col min-w-0">
              <span class="font-semibold text-sm text-slate-800 truncate">{{ user.name }}</span>
              <span class="text-xs text-slate-400 truncate">{{ user.email }}</span>
            </div>
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
        <div class=" h-14 border-b border-slate-200 bg-white px-6 flex items-center justify-between shadow-2xs">

          <button @click="chatStore.activeChatId = null"
                  class="md:hidden mr-2 p-1 text-slate-500 hover:text-slate-700 cursor-pointer">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-xs text-slate-600 uppercase">
              <div
                  @click="isAvatarModalOpen = true"
                  class="flex h-full w-full items-center justify-center rounded-full bg-indigo-100 font-semibold text-xl text-indigo-700 uppercase tracking-wider overflow-hidden border border-indigo-200/50 shadow-2xs cursor-pointer"
                  title="Просмотреть фото"
              >
                <img
                    v-if="chatStore.activeInterlocutor?.avatar_path"
                    :src="chatStore.activeInterlocutor.avatar_path"
                    alt="Аватар"
                    class="h-full w-full object-cover rounded-full transition-transform duration-200 group-hover:scale-105"
                />
                <template v-else>
                  {{chatStore.activeInterlocutor?.name ? chatStore.activeInterlocutor.name.charAt(0) : 'U' }}
                </template>
              </div>
            </div>
            <div class="flex flex-col">
              <span class="font-bold text-sm text-slate-800">
                {{chatStore.activeInterlocutor ? chatStore.activeInterlocutor.name : 'Чат'}}
              </span>
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
        <div ref="messageContainer" class="flex-1 flex flex-col  p-4 md:p-6 overflow-y-auto space-y-4">

          <div class="flex-1"></div>

          <div
              v-for="msg in chatStore.messages"
              :key="msg.id"
              class="flex flex-col max-w-[85%] md:max-w-[70%] group"
              :class="[
                isMyMessage(msg.user_id) ? 'ml-auto items-end' : 'items-start',
                index === 0 ? 'mt-auto' : ''
              ]"
          >
            <!-- Имя отправителя (для чужих) -->
            <span v-if="!isMyMessage(msg.user_id)" class="text-xs text-slate-400 font-medium ml-2 mb-1">
              {{ msg.user?.name }}
            </span>

            <!-- Контейнер: Облачко + Кнопки действий (карандаш/корзина) -->
            <div class="flex items-center gap-2 max-w-full" :class="isMyMessage(msg.user_id) ? 'flex-row-reverse' : 'flex-row'">

              <!-- Облачко сообщения -->
              <div
                  class="px-4 py-2 text-sm shadow-2xs leading-relaxed break-words w-fit max-w-full relative"
                  :class="isMyMessage(msg.user_id)
                  ? 'bg-indigo-600 text-white rounded-2xl rounded-br-none pl-4 pr-4 pb-5'
                  : 'bg-white text-slate-800 border border-slate-100 rounded-2xl rounded-bl-none pl-4 pr-4 pb-5'"
              >
                <!-- Вывод текста -->
                <span v-if="msg.text" class="block whitespace-pre-wrap">{{ msg.text }}</span>

                <!-- Вывод вложений (attachments) -->
                <div v-if="msg.attachments && msg.attachments.length > 0" class="space-y-2 mt-2">
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

                <!-- СЛУЖЕБНЫЙ БЛОК: Время, Изменено, Галочки (Позиционируется абсолютно внизу справа облачка) -->
                <div class="absolute bottom-1 right-2.5 flex items-center gap-1 text-[10px]" :class="isMyMessage(msg.user_id) ? 'text-indigo-200' : 'text-slate-400'">

                  <!-- Метка "изм." -->
                  <span v-if="isEdited(msg)" class="font-medium italic opacity-80">изм.</span>

                  <!-- Время отправки -->
                  <span>{{ formatTime(msg.created_at) }}</span>

                  <!-- ГАЛОЧКИ ДОСТАВКИ (Только для наших сообщений) -->
                  <div v-if="isMyMessage(msg.user_id)" class="ml-0.5 shrink-0">
                    <!-- Две галочки (Прочитано) -->
                    <svg v-if="msg.read_at" class="h-3.5 w-3.5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7M5 18l4 4L19 12" />
                    </svg>
                    <!-- Одна галочка (Доставлено) -->
                    <svg v-else class="h-3.5 w-3.5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>

              </div>

              <!-- КНОПКИ ДЕЙСТВИЙ: Появляются только при наведении мышки (group-hover) и только для НАШИХ сообщений -->
              <div v-if="isMyMessage(msg.user_id)" class="flex items-center bg-white border border-slate-100 rounded-lg shadow-2xs opacity-0 group-hover:opacity-100 transition-opacity">
                <!-- Редактировать -->
                <button @click="startEdit(msg)" type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-l-lg hover:bg-slate-50 cursor-pointer" title="Редактировать">
                  <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </button>
                <!-- Удалить -->
                <button @click="deleteMessage(msg.id)" type="button" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-r-lg hover:bg-rose-50 border-l border-slate-100 cursor-pointer" title="Удалить для всех">
                  <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>

            </div>
          </div>
        </div>


        <!-- Лента сообщений -->

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


            <!-- Индикатор режима редактирования -->
            <div v-if="editingMessageId" class="flex items-center justify-between px-3 py-1.5 bg-blue-50/70 border-l-4 border-blue-600 text-xs text-blue-800 rounded-r-xl mb-1">
              <div class="flex items-center gap-1.5 truncate">
                <svg class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                <span class="font-medium truncate">Редактирование сообщения...</span>
              </div>
              <button @click="cancelEdit" type="button" class="text-blue-500 hover:text-blue-700 font-semibold uppercase text-[10px] tracking-wider cursor-pointer">
                Отмена
              </button>
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
                  :disabled="!newMessageText && selectedFiles.length === 0"
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

  <!-- МОДАЛЬНОЕ ОКНО ДЛЯ ПРОСМОТРА АВАТАРА НА ВЕСЬ ЭКРАН -->
  <div
      v-if="isAvatarModalOpen && chatStore.activeInterlocutor?.avatar_path"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4"
      @click="isAvatarModalOpen = false"
  >
    <!-- Крестик для закрытия -->
    <button
        type="button"
        @click="isAvatarModalOpen = false"
        class="absolute top-4 right-4 p-2 text-white/70 hover:text-white rounded-full bg-white/10 hover:bg-white/20 transition-colors cursor-pointer"
    >
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Крупное изображение (Ограничено по высоте экрана, чтобы не ломать верстку) -->
    <div class="max-w-md w-full max-h-[80vh] p-2 bg-white rounded-3xl shadow-2xl overflow-hidden">
      <img
          :src="chatStore.activeInterlocutor.avatar_path"
          alt="Большой аватар"
          class="w-full h-auto max-h-[75vh] object-cover rounded-2xl shadow-inner"
      />
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
const editingMessageId = ref(null);
let currentUserId = authStore.user?.id || null;
const isAvatarModalOpen = ref(false);

const isMyMessage = (id) => id === currentUserId;
const BACKEND_URL = axios.defaults.baseURL;

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
      // 1. Слушаем отправку НОВЫХ сообщений
      .listen('MessageSent', (e) => {
        // Обновляем превью последнего сообщения в списке чатов слева
        const targetChat = chatStore.chatList.find(c => c.id === id);
        if (targetChat) {
          targetChat.last_message = e;
        }

        // Если этот чат сейчас открыт на экране — пушим в ленту
        if (chatStore.activeChatId === id) {
          if (!chatStore.messages.some(m => m.id === e.id)) {
            chatStore.messages.push(e);
          }
          scrollToBottom();
          chatStore.readChatMessages(id); // Шлем бэку статус "прочитано"
        } else {
          // Если чат в фоне — увеличиваем зеленый кружок счетчика
          if (targetChat) {
            targetChat.unread_count = (targetChat.unread_count || 0) + 1;
          }
        }
      })

      // 2. Слушаем РЕДАКТИРОВАНИЕ сообщений (Собеседник изменил текст)
      .listen('MessageUpdated', (e) => {
        // Находим измененное сообщение в нашей ленте и обновляем его контент
        const idx = chatStore.messages.findIndex(m => m.id === e.message.id);
        if (idx !== -1) {
          chatStore.messages[idx] = e.message;
        }

        // Также обновляем превью в левой панели, если это было самое последнее сообщение
        const targetChat = chatStore.chatList.find(c => c.id === id);
        if (targetChat && targetChat.last_message?.id === e.message.id) {
          targetChat.last_message = e.message;
        }
      })

      // 3. Слушаем УДАЛЕНИЕ сообщений (Собеседник удалил у себя для всех)
      .listen('MessageDeleted', (e) => {
        // Мгновенно стираем удаленное облачко с экрана
        chatStore.messages = chatStore.messages.filter(m => m.id !== e.messageId);

        // Если удалили последнее сообщение — запрашиваем список чатов заново для обновления превью
        const targetChat = chatStore.chatList.find(c => c.id === id);
        if (targetChat && targetChat.last_message?.id === e.messageId) {
          // Чтобы не писать сложную логику поиска предыдущего сообщения, просто обновляем список
          chatStore.getChatList();
        }
      });

/*  window.Echo.private(`chat.${id}`)
      .listen('MessageSent', (e) => {

        if (!chatStore.messages.some(m => m.id === e.id)) {
           chatStore.messages.push(e.message);
         }
        chatStore.messages.push(e);
        scrollToBottom();
        chatStore.readChatMessages(id);
      });*/
};


// Проверка, редактировалось ли сообщение
function isEdited(msg) {
  return msg.created_at !== msg.updated_at;
}

// Функция форматирования времени (из ISO в ЧЧ:ММ)
function formatTime(isoString) {
  if (!isoString) return '';
  const date = new Date(isoString);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Начало редактирования: переносим текст в инпут
function startEdit(msg) {
  editingMessageId.value = msg.id;
  newMessageText.value = msg.text; // Подставляем текст в инпут
}

// Отмена редактирования
function cancelEdit() {
  editingMessageId.value = null;
  newMessageText.value = '';
}

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


// ФУНКЦИЯ УДАЛЕНИЯ СООБЩЕНИЯ
async function deleteMessage(messageId) {
  if (!confirm('Удалить это сообщение для всех?')) return;

  try {
    await axios.delete(`/api/messages/${messageId}`);
    // Удаляем локально с экрана
    chatStore.messages = chatStore.messages.filter(m => m.id !== messageId);
  } catch (error) {
    console.error('Не удалось удалить сообщение:', error);
  }
}

// Отправка сообщения
const sendMessage = async () => {

  if (!newMessageText.value.trim() && selectedFiles.value.length === 0) return;

  // ЕСЛИ МЫ РЕДАКТИРУЕМ СУЩЕСТВУЮЩЕЕ СООБЩЕНИЕ:
  if (editingMessageId.value) {
    try {
      const response = await axios.put(`/api/messages/${editingMessageId.value}`, {
        text: newMessageText.value
      });

      // Обновляем сообщение локально на экране
      const idx = chatStore.messages.findIndex(m => m.id === editingMessageId.value);
      if (idx !== -1) chatStore.messages[idx] = response.data;

      cancelEdit(); // Сбрасываем режим редактирования
    } catch (error) {
      console.error('Не удалось отредактировать:', error);
    }
    return;
  }

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
