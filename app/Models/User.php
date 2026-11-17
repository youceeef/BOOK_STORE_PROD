<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Keep if you require email verification
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable; // <-- Import the Billable trait

class User extends Authenticatable // implements MustVerifyEmail (optional)
{
    use HasApiTokens, HasFactory, Notifiable, Billable; // <-- Add Billable here

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
}
