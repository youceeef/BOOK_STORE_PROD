<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id')->toArray();
        if (empty($categoryIds)) {
            $catId = DB::table('categories')->insertGetId([
                'name' => 'Fiction',
                'slug' => 'fiction',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryIds = [$catId];
        }

        $covers = [
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1532012164546-f432f2e3777f?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1524578271613-d550eacf6090?w=600&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=600&auto=format&fit=crop&q=80',
        ];

        $titles = [
            'Whispers in the Wind',
            'Echoes of Eternity',
            'The Last Voyage',
            'Crimson Skies',
            'Tales from the Riverbank',
            'Shattered Dreams',
            'City of Lanterns',
            'Paths Untold',
            'Garden of Shadows',
            'Beneath the Ice',
            'Threads of Fate',
            'Rise of the Phoenix',
            'The Forgotten Realm',
            'Winds of Change',
            'Labyrinth of Souls',
            'Moonlight Sonata',
            'Voices of the Deep',
            'Crown of Ash',
            'Starlit Journey',
            'Embers and Ashes',
        ];

        $books = [];

        foreach ($titles as $index => $title) {
            $books[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'category_id' => $categoryIds[$index % count($categoryIds)],
                'author' => 'Author ' . chr(65 + $index),
                'description' => "An engaging and captivating edition of {$title}. Explores themes of human nature, destiny, and resilience across historical epochs.",
                'isbn' => '9780000000' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'cover_image' => $covers[$index % count($covers)],
                'price' => rand(14, 45) + 0.99,
                'stock' => rand(3, 25),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('books')->truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('books')->insert($books);
    }
}
