<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'author',
        'description',
        'sample_excerpt',
        'isbn',
        'cover_image',
        'price',
        'stock'
    ];

    // A Book belongs to a Category
    public function category(): BelongsTo // Type hint
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound route value.
     * Supports both slug (e.g. /books/the-great-gatsby) and numeric ID (e.g. /admin/books/21/edit).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        if (is_numeric($value)) {
            $book = $this->where('id', $value)->first();
            if ($book) {
                return $book;
            }
        }

        return $this->where('slug', $value)->firstOrFail();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isWishlistedBy(?User $user = null): bool
    {
        if ($user) {
            return $this->wishlists()->where('user_id', $user->id)->exists();
        }

        $guestWishlist = session('guest_wishlist', []);
        return in_array($this->id, $guestWishlist);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function averageRating(): float
    {
        return (float) round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function reviewsCount(): int
    {
        return (int) $this->reviews()->count();
    }

    public function ratingBreakdown(): array
    {
        $total = $this->reviewsCount();
        $breakdown = [];

        for ($star = 5; $star >= 1; $star--) {
            $count = $this->reviews()->where('rating', $star)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $breakdown[$star] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return $breakdown;
    }

    public function hasSample(): bool
    {
        return !empty(trim($this->sample_excerpt ?? ''));
    }

    public function readingTime(): int
    {
        $words = str_word_count(strip_tags($this->sample_excerpt ?? ''));
        return max(1, (int) ceil($words / 200));
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }

    public function availableStock(?string $sessionId = null, ?int $userId = null): int
    {
        return \App\Services\StockReservationService::getAvailableStock($this, $sessionId, $userId);
    }

    public function getCoverUrlAttribute(): string
    {
        if (!$this->cover_image) {
            return asset('images/covers/default.png');
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        if (str_starts_with($this->cover_image, 'images/')) {
            return asset($this->cover_image);
        }

        $mediaDisk = config('filesystems.media_disk', 'public');
        $relative = ltrim(str_replace('/storage/', '', $this->cover_image), '/');

        return Storage::disk($mediaDisk)->url($relative);
    }
}
