<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('fulfillment_status')->default('unfulfilled')->after('payment_status');
            $table->timestamp('confirmation_sent_at')->nullable()->after('delivered_at');
        });

        // Backfill existing orders
        DB::statement("
            UPDATE orders SET 
                payment_status = CASE 
                    WHEN status IN ('paid', 'processing', 'shipped', 'delivered', 'completed') THEN 'paid'
                    WHEN status = 'failed' THEN 'failed'
                    ELSE 'pending'
                END,
                fulfillment_status = CASE 
                    WHEN status IN ('processing', 'shipped', 'delivered', 'cancelled') THEN status
                    ELSE 'unfulfilled'
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'fulfillment_status', 'confirmation_sent_at']);
        });
    }
};
