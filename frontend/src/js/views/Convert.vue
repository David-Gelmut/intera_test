<template>
  <div class="space-y-6">
    <!-- Заголовок страницы -->
    <div class="border-b border-slate-200 pb-5">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Распознавание текста</h1>
      <p class="mt-1 text-sm text-slate-500">Оптическое распознавание символов (Tesseract OCR) и конвертация документов.</p>
    </div>

    <div class="max-w-3xl">
      <!-- Основная карточка формы -->
      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Параметры конвертации</h2>
        <p class="text-sm text-slate-500 mb-6">Загрузите изображение с текстом и выберите желаемый формат документа.</p>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <!-- Поле выбора файла (Drag and Drop Зона) -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Изображение (PNG, JPG)</label>
            <div class="relative flex justify-center rounded-lg border-2 border-dashed border-slate-300 px-6 py-8 transition-colors hover:border-slate-400 bg-slate-50/50">
              <div class="text-center space-y-1">
                <!-- SVG Иконка загрузки -->
                <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="flex text-sm text-slate-600 justify-center">
                  <label for="image" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                    <span>Выберите файл</span>
                    <input
                        id="image"
                        type="file"
                        accept="image/*"
                        @change="handleFileChange"
                        required
                        class="sr-only"
                    />
                  </label>
                  <p class="pl-1">или перетащите его сюда</p>
                </div>
                <p class="text-xs text-slate-400">Допускаются изображения до 10 МБ</p>
              </div>
            </div>
            <!-- Дополнительный блок для отображения имени выбранного файла, если необходимо реализовать в скрипте -->
            <p v-if="file" class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Файл готов к отправке
            </p>
          </div>

          <!-- Поле выбора формата -->
          <div class="flex flex-col gap-1.5">
            <label for="format" class="text-xs font-semibold uppercase tracking-wider text-slate-500">Формат готового файла</label>
            <div class="relative">
              <select
                  id="format"
                  v-model="format"
                  class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm shadow-xs appearance-none cursor-pointer"
              >
                <option value="txt">Текстовый файл (.txt)</option>
                <option value="pdf">Документ PDF (.pdf)</option>
                <option value="tsv">Формат TSV (.tsv)</option>
                <option value="hocr">Формат разметки HTML (.hocr)</option>
              </select>
              <!-- Кастомная стрелочка для селекта -->
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Кнопка отправки -->
          <button
              type="submit"
              :disabled="isLoading"
              class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-blue-400 disabled:cursor-not-allowed inline-flex justify-center items-center gap-2 cursor-pointer"
          >
            <svg v-if="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? 'Раполнивание и конвертация...' : 'Конвертировать и Скачать' }}
          </button>
        </form>

        <!-- Сообщение об ошибке -->
        <div v-if="errorMessage" class="mt-4 p-4 bg-rose-50 border border-rose-100 text-sm text-rose-700 rounded-lg flex gap-2 items-start">
          <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            imageFile: null,
            format: 'txt', // формат по умолчанию
            isLoading: false,
            errorMessage: ''
        };
    },
    methods: {
        handleFileChange(event) {
            this.imageFile = event.target.files[0];
            this.errorMessage = '';
        },
        async handleSubmit() {
            if (!this.imageFile) {
                this.errorMessage = 'Пожалуйста, выберите файл изображения.';
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';

            // Подготавливаем FormData для отправки файла
            const formData = new FormData();
            formData.append('image', this.imageFile);
            formData.append('format', this.format);

            try {
                // Делаем запрос к Laravel API с типом ответа blob (для скачивания файлов)
                const response = await axios.post('/api/convert', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    responseType: 'blob'
                });

                // Получаем имя файла из заголовков ответа или задаем стандартное
                const extension = this.format;
                const downloadName = `recognized_document_${Date.now()}.${extension}`;

                // Создаем временную ссылку в браузере для скачивания файла
                const blob = new Blob([response.data]);
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = downloadName;
                link.click();

                // Очищаем ссылку
                window.URL.revokeObjectURL(link.href);

            } catch (error) {
                console.error(error);
                this.errorMessage = 'Произошла ошибка при обработке файла. Проверьте логи сервера.';
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>

