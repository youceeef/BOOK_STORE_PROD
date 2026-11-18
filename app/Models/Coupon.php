<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon for date handling

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'expires_at',
        'usage_limit',
        // 'usage_count' // Usually not fillable, managed internally
    ];

    protected $casts = [
        'expires_at' => 'datetime', // Cast expires_at to Carbon instance
        'value' => 'decimal:2',      // Ensure value is treated as decimal
        'min_amount' => 'decimal:2', // Ensure min_amount is treated as decimal
    ];

    /**
     * Calculate the discount amount for a given total.
     *
     * @param float $total
     * @return float
     */
    public function calculateDiscount(float $total): float
    {
        if ($this->type === 'fixed') {
            // Ensure discount doesn't exceed total
            return min((float) $this->value, $total);
        } elseif ($this->type === 'percent') {
            $discount = round(((float) $this->value / 100) * $total, 2);
            return min($discount, $total);
        } else {
            return 0; // Should not happen
        }
    }

    /**
     * Check if the coupon is still valid (not expired, within usage limits).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $isExpired = false;
        if ($this->expires_at) {
            // Treat date-only / midnight expiry as end of that day
            $expiry = $this->expires_at->copy();
            if ($expiry->format('H:i:s') === '00:00:00') {
                $expiry = $expiry->endOfDay();
            }
            $isExpired = $expiry->isPast();
        }

        $usageLimitReached = $this->usage_limit !== null && $this->usage_count >= $this->usage_limit;

        return !$isExpired && !$usageLimitReached;
    }
}
