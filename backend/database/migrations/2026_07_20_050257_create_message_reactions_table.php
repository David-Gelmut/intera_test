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
        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();   
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();           
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();      
            // Сам символ эмодзи. Достаточно varchar(191) с поддержкой utf8mb4
            $table->string('emoji');
            $table->timestamps();
            // Уникальный индекс, чтобы один юзер не мог поставить ОДИН И ТОТ ЖЕ эмодзи дважды
            $table->unique(['message_id', 'user_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_reactions');
    }
};
