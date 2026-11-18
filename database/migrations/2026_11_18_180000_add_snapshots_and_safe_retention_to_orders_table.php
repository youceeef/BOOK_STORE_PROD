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
            $table->string('customer_name_snapshot')->nullable()->after('user_id');
            $table->string('customer_email_snapshot')->nullable()->after('customer_name_snapshot');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('total_amount');
            $table->string('refund_id')->nullable()->after('refunded_amount');
            $table->string('cancelled_reason')->nullable()->after('fulfillment_notes');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_reason');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('title_snapshot')->nullable()->after('book_id');
            $table->string('author_snapshot')->nullable()->after('title_snapshot');
        });

        // Backfill snapshots from existing relationships
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            if ($order->user_id) {
                $user = DB::table('users')->where('id', $order->user_id)->first();
                if ($user) {
                    DB::table('orders')->where('id', $order->id)->update([
                        'customer_name_snapshot' => $user->name,
                        'customer_email_snapshot' => $user->email,
                    ]);
                }
            }
        }

        $items = DB::table('order_items')->get();
        foreach ($items as $item) {
            if ($item->book_id) {
                $book = DB::table('books')->where('id', $item->book_id)->first();
                if ($book) {
                    DB::table('order_items')->where('id', $item->id)->update([
                        'title_snapshot' => $book->title,
                        'author_snapshot' => $book->author,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['title_snapshot', 'author_snapshot']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name_snapshot',
                'customer_email_snapshot',
                'refunded_amount',
                'refund_id',
                'cancelled_reason',
                'cancelled_at',
            ]);
        });
    }
};
