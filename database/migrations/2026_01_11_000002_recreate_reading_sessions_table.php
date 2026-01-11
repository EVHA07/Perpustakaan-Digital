<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reading_sessions');

        Schema::create('reading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('last_ping_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'book_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_sessions');
    }
};
