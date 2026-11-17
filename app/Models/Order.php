<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import
use Illuminate\Database\Eloquent\Relations\HasMany; // Import

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'carrier',
        'tracking_number',
        'estimated_delivery_at',
        'shipped_at',
        'delivered_at',
        'fulfillment_notes',
        'total_amount',
        'stripe_payment_intent_id',
        'billing_address',
        'shipping_address',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'estimated_delivery_at' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get numeric progression step index for the fulfillment timeline.
     * 1 = Placed / Confirmed
     * 2 = Processing / Archival QC
     * 3 = Shipped / In Transit
     * 4 = Delivered
     * -1 = Cancelled
     */
    public function getStepIndex(): int
    {
        return match($this->status) {
            'pending' => 1,
            'paid' => 1,
            'processing' => 2,
            'shipped' => 3,
            'delivered' => 4,
            'cancelled' => -1,
            default => 1,
        };
    }

    /**
     * Generate dynamic tracking URL based on carrier name.
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->tracking_number) {
            return null;
        }

        $carrier = strtolower($this->carrier ?? '');
        $code = urlencode($this->tracking_number);

        if (str_contains($carrier, 'dhl')) {
            return "https://www.dhl.com/en/express/tracking.html?AWB={$code}";
        }
        if (str_contains($carrier, 'fedex')) {
            return "https://www.fedex.com/fedextrack/?trknbr={$code}";
        }
        if (str_contains($carrier, 'ups')) {
            return "https://www.ups.com/track?tracknum={$code}";
        }
        if (str_contains($carrier, 'royal') || str_contains($carrier, 'mail')) {
            return "https://www.royalmail.com/track-your-item#/tracking-results/{$code}";
        }
        if (str_contains($carrier, 'usps')) {
            return "https://tools.usps.com/go/TrackConfirmAction?tLabels={$code}";
        }

        return "https://parcelsapp.com/en/tracking/{$code}";
    }

    // An Order belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // An Order has many OrderItems
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
