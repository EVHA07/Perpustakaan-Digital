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
        Schema::table('histories', function (Blueprint $table) {
            $table->timestamp('last_ping_at')->nullable()->after('total_time_spent');
            $table->unsignedBigInteger('total_time_spent')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('last_ping_at');
            $table->integer('total_time_spent')->default(0)->change();
        });
    }
};
