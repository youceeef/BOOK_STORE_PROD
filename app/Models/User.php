<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'bio',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Add relationship to Orders (we'll create Order model soon)
    public function orders() // Assuming Order model exists or will exist
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Wishlist entries for the user.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * The books saved by this user.
     */
    public function wishlistBooks()
    {
        return $this->belongsToMany(Book::class, 'wishlists')->withTimestamps();
    }

    /**
     * Check if a specific book is in the user's wishlist.
     */
    public function hasInWishlist(int|string $bookId): bool
    {
        return $this->wishlists()->where('book_id', $bookId)->exists();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function hasReviewed(Book $book): bool
    {
        return $this->reviews()->where('book_id', $book->id)->exists();
    }

    public function hasPurchased(Book $book): bool
    {
        return $this->orders()
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhereIn('status', ['paid', 'completed', 'shipped', 'delivered', 'processing']);
            })
            ->whereHas('items', function ($query) use ($book) {
                $query->where('book_id', $book->id);
            })
            ->exists();
    }

    /**
     * Resolves the public URL for the user's custom avatar or returns null.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $disk = config('filesystems.media_disk', 'public');
        if ($this->avatar && Storage::disk($disk)->exists($this->avatar)) {
            return Storage::disk($disk)->url($this->avatar);
        }
        return null;
    }

    /**
     * Formatted multi-part address for shipping and invoices.
     */
    public function getFormattedAddressAttribute(): ?string
    {
        if (!$this->address_line1) {
            return null;
        }

        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Send the custom editorial email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail());
    }

    /**
     * Send the custom editorial password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
