<template>
    <div class="ocr-container">
        <h2>Распознавание текста (Tesseract OCR)</h2>

        <form @submit.prevent="handleSubmit" class="ocr-form" >
            <!-- Поле выбора файла -->
            <div class="form-group">
                <label for="image">Выберите изображение (PNG, JPG):</label>
                <input
                    type="file"
                    id="image"
                    accept="image/*"
                    @change="handleFileChange"
                    required
                />
            </div>

            <!-- Поле выбора формата -->
            <div class="form-group">
                <label for="format">Формат готового файла:</label>
                <select id="format" v-model="format">
                    <option value="txt">Текстовый файл (.txt)</option>
                    <option value="pdf">Документ PDF (.pdf)</option>
                    <option value="tsv">Формат TSV (.tsv)</option>
                    <option value="hocr">Формат разметки HTML (.hocr)</option>
                </select>
            </div>

            <!-- Кнопка отправки -->
            <button type="submit" :disabled="isLoading" class="btn-submit">
                {{ isLoading ? 'Распознавание и конвертация...' : 'Конвертировать и Скачать' }}
            </button>
        </form>

        <!-- Сообщение об ошибке -->
        <div v-if="errorMessage" class="error-block">
            {{ errorMessage }}
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
                const response = await axios.post('/convert', formData, {
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

<style scoped>
/*.ocr-container {
    max-width: 500px;
    margin: 40px auto;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.ocr-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
input, select {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.btn-submit {
    padding: 10px;
    background-color: #4f46e5;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}
.btn-submit:disabled {
    background-color: #a5b4fc;
    cursor: not-allowed;
}
.error-block {
    margin-top: 15px;
    padding: 10px;
    background-color: #fee2e2;
    color: #ef4444;
    border-radius: 4px;
}*/
</style>

