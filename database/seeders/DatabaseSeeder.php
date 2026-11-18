<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        $admin = User::firstWhere('email', $adminEmail);
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
            ]);
        } elseif (!app()->isProduction()) {
            $admin->update([
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
            ]);
        }

        // 2. Demo Customer User (local/testing only)
        $customer = null;
        if (!app()->isProduction()) {
            $customer = User::firstWhere('email', 'customer@example.com');
            if (!$customer) {
                $customer = User::create([
                    'name' => 'Jane Reader',
                    'email' => 'customer@example.com',
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]);
            } else {
                $customer->update([
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]);
            }
        }

        // 3. Categories
        $categories = [
            'Fiction',
            'Science & Technology',
            'History & Biography',
            'Philosophy & Arts',
            'Classic Literature',
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat, 'active' => true]
            );
        }

        // 4. Books (always seed 20 fresh books)
        $this->call([
            BookSeeder::class,
            ReviewSeeder::class,
            SampleExcerptSeeder::class,
        ]);

        // 5. Promotional Coupons
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_amount' => 20,
                'usage_limit' => 100,
                'expires_at' => now()->addMonths(6),
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'SAVE5'],
            [
                'type' => 'fixed',
                'value' => 5,
                'min_amount' => 15,
                'usage_limit' => 100,
                'expires_at' => now()->addMonths(3),
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'VIP25'],
            [
                'type' => 'percent',
                'value' => 25,
                'min_amount' => 50,
                'usage_limit' => 50,
                'expires_at' => now()->addYear(),
            ]
        );

        // 6. Sample Demo Orders (local/testing only)
        if (!app()->isProduction() && $customer) {
            $firstBooks = Book::take(3)->get();
            if ($firstBooks->count() >= 2 && Order::count() === 0) {
            $order1 = Order::create([
                'user_id' => $customer->id,
                'status' => 'paid',
                'total_amount' => $firstBooks[0]->price + $firstBooks[1]->price,
                'stripe_payment_intent_id' => 'pi_demo_sample_001',
                'billing_address' => '124 Oxford Street, London W1D 1BS',
                'shipping_address' => '124 Oxford Street, London W1D 1BS',
                'coupon_code' => 'WELCOME10',
                'discount_amount' => round(($firstBooks[0]->price + $firstBooks[1]->price) * 0.10, 2),
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'book_id' => $firstBooks[0]->id,
                'quantity' => 1,
                'price' => $firstBooks[0]->price,
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'book_id' => $firstBooks[1]->id,
                'quantity' => 1,
                'price' => $firstBooks[1]->price,
            ]);

            $order2 = Order::create([
                'user_id' => $customer->id,
                'status' => 'shipped',
                'total_amount' => $firstBooks[0]->price * 2,
                'stripe_payment_intent_id' => 'pi_demo_sample_002',
                'billing_address' => '42 Baker Street, London NW1 6XE',
                'shipping_address' => '42 Baker Street, London NW1 6XE',
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'book_id' => $firstBooks[0]->id,
                'quantity' => 2,
                'price' => $firstBooks[0]->price,
            ]);
        }
    }
}
}
