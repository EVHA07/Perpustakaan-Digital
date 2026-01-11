<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_book_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->unsignedInteger('total_seconds')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
            $table->index(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_book_stats');
    }
};
