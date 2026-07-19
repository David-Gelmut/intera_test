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
        Schema::table('messages', function (Blueprint $table) {
             // Добавляем внешний ключ на саму себя (таблицу messages)
            // nullable() обязателен, так как большинство сообщений — обычные, а не ответы
            // cascadeOnDelete() удалит цепочку ответов, если удалить оригинал (или используйте nullOnDelete)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->after('chat_id') // размещаем после ID чата
                  ->constrained('messages')
                  ->nullOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
             // Удаляем связь и сам столбец при откате миграции
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
