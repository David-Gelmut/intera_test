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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('yandex_author_id', 64);
            $table->string('yandex_author_name');
            $table->unsignedTinyInteger('rating');
            $table->text('text')->nullable();
            $table->dateTime('published_at');
            $table->string('review_hash', 64)->unique();

            $table->unique(['company_id', 'yandex_author_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
