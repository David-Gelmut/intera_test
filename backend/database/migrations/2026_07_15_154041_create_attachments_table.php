<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');

            $table->string('file_path');  // Ссылка на файл (или ключ для S3)
            $table->string('file_name');  // Оригинальное имя (photo.jpg)
            $table->string('file_type');  // 'image' или 'file' (для верстки во Vue)
            $table->string('driver')->default('local'); // Задел на будущее ('s3', 'yandex')
            $table->unsignedBigInteger('file_size')->nullable(); // Размер в байтах (удобно для UI)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
