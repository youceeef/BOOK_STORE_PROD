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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('carrier')->nullable()->after('status');
            $table->string('tracking_number')->nullable()->after('carrier');
            $table->date('estimated_delivery_at')->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('estimated_delivery_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->text('fulfillment_notes')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'carrier',
                'tracking_number',
                'estimated_delivery_at',
                'shipped_at',
                'delivered_at',
                'fulfillment_notes',
            ]);
        });
    }
};
