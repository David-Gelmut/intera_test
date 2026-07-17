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
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            // Кого посмотрели (Мастер)
            $table->foreignId('master_id')->constrained('users')->onDelete('cascade');
            // Кто посмотрел (Заказчик). Nullable, если зашел неавторизованный гость
            $table->foreignId('viewer_id')->nullable()->constrained('users')->onDelete('set null');

            // Метаданные для аналитики
            $table->string('city')->nullable(); // Город посетителя (для гео-аналитики)
            $table->string('source')->nullable(); // Откуда пришел ('search', 'forum', 'blog')

            $table->timestamp('viewed_at')->useCurrent(); // Время просмотра (вместо тяжелых timestamps)

            // Индексы для мгновенной выборки графиков
            $table->index(['master_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_views');
    }
};
