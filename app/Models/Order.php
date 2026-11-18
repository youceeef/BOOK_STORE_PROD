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
        'customer_name_snapshot',
        'customer_email_snapshot',
        'status',
        'payment_status',
        'fulfillment_status',
        'carrier',
        'tracking_number',
        'estimated_delivery_at',
        'shipped_at',
        'delivered_at',
        'confirmation_sent_at',
        'fulfillment_notes',
        'cancelled_reason',
        'cancelled_at',
        'total_amount',
        'refunded_amount',
        'refund_id',
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
        'confirmation_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getCustomerNameAttribute(): string
    {
        return $this->customer_name_snapshot ?? $this->user?->name ?? 'Valued Customer';
    }

    public function getCustomerEmailAttribute(): string
    {
        return $this->customer_email_snapshot ?? $this->user?->email ?? 'N/A';
    }

    /**
     * Return formatted human-readable shipping address snapshot.
     */
    public function getFormattedShippingAddressAttribute(): string
    {
        $raw = $this->shipping_address ?? $this->billing_address;
        if (!$raw) {
            return 'Standard Delivery';
        }
        $data = is_array($raw) ? $raw : json_decode($raw, true);
        if (!is_array($data)) {
            return (string) $raw;
        }
        $lines = array_filter([
            $data['name'] ?? null,
            $data['address'] ?? null,
            trim(($data['city'] ?? '') . ' ' . ($data['postal_code'] ?? '')),
        ]);
        return implode(', ', $lines) ?: 'Standard Delivery';
    }

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

    public function getPaymentStatusAttribute(?string $value): string
    {
        if (!empty($value) && $value !== 'pending') {
            return $value;
        }
        if (($this->attributes['status'] ?? null) === 'paid') {
            return 'paid';
        }
        return $value ?: 'pending';
    }

    public function getFulfillmentStatusAttribute(?string $value): string
    {
        if (!empty($value) && $value !== 'unfulfilled') {
            return $value;
        }
        $status = $this->attributes['status'] ?? null;
        if (in_array($status, ['shipped', 'delivered', 'cancelled'])) {
            return $status;
        }
        return $value ?: 'unfulfilled';
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
