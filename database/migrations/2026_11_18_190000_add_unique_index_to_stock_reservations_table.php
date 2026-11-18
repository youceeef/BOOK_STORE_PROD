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
        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->unique(['session_id', 'book_id'], 'stock_reservations_session_book_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->dropUnique('stock_reservations_session_book_unique');
        });
    }
};
