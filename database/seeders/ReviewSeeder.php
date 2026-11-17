<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();
        if ($books->isEmpty()) {
            return;
        }

        // Create a couple of reviewer personas if they don't exist
        $reviewers = [
            [
                'email' => 'clara.wood@example.com',
                'name' => 'Clara Wood',
            ],
            [
                'email' => 'arthur.dent@example.com',
                'name' => 'Arthur Dent',
            ],
            [
                'email' => 'elena.rostova@example.com',
                'name' => 'Elena Rostova',
            ],
            [
                'email' => 'customer@example.com',
                'name' => 'Jane Reader',
            ],
        ];

        $users = [];
        foreach ($reviewers as $r) {
            $users[] = User::firstOrCreate(
                ['email' => $r['email']],
                [
                    'name' => $r['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]
            );
        }

        $sampleReviews = [
            [
                'rating' => 5,
                'title' => 'An exceptional archival printing and binding',
                'comment' => 'The paper quality and typography in this edition are extraordinary. Reading this felt like experiencing the prose in its intended physical weight. Tracked delivery arrived in pristine packaging.',
                'is_verified' => true,
                'helpful' => 14,
            ],
            [
                'rating' => 5,
                'title' => 'Masterful prose and luminous translation',
                'comment' => 'One of the most moving works I have revisited this decade. The cadence of the language holds up effortlessly. Highly recommended for any serious literary library.',
                'is_verified' => true,
                'helpful' => 9,
            ],
            [
                'rating' => 4,
                'title' => 'Compelling narrative with deep historical resonance',
                'comment' => 'The character arcs are beautifully paced. The binding and typeface are clean and easy on the eyes during prolonged evening reading sessions.',
                'is_verified' => false,
                'helpful' => 5,
            ],
            [
                'rating' => 5,
                'title' => 'A permanent cornerstone on my bookshelf',
                'comment' => 'Essential reading. Fast fulfillment, accurate categorization, and wonderful texture on the dust jacket. Will certainly purchase complementary titles from Lara Bookstore.',
                'is_verified' => true,
                'helpful' => 22,
            ],
        ];

        // Seed 2 to 3 reviews per book for the first 8 books
        foreach ($books->take(8) as $index => $book) {
            foreach ($users as $uIndex => $user) {
                if (($index + $uIndex) % 2 === 0) {
                    $template = $sampleReviews[($index + $uIndex) % count($sampleReviews)];

                    Review::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'book_id' => $book->id,
                        ],
                        [
                            'rating' => $template['rating'],
                            'title' => $template['title'],
                            'comment' => $template['comment'],
                            'is_verified_purchase' => $template['is_verified'],
                            'helpful_count' => $template['helpful'],
                        ]
                    );
                }
            }
        }
    }
}
