<?php

namespace App\Services;

use App\Models\Book;
use App\Models\StockReservation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class StockReservationService
{
    /**
     * Default reservation duration in minutes.
     */
    public const DEFAULT_HOLD_MINUTES = 15;

    /**
     * Reserve inventory for all items in a cart using pessimistic row locking.
     *
     * @param User|null $user
     * @param iterable $cartItems
     * @param string $sessionId
     * @param int $holdMinutes
     * @return array ['success' => bool, 'error' => ?string]
     */
    public static function reserveCart(?User $user, iterable $cartItems, string $sessionId, int $holdMinutes = self::DEFAULT_HOLD_MINUTES): array
    {
        try {
            return DB::transaction(function () use ($user, $cartItems, $sessionId, $holdMinutes) {
                // First prune expired reservations to maintain clean availability
                self::pruneExpired();

                $userId = $user?->id;
                $expiresAt = now()->addMinutes($holdMinutes);
                $cartBookIds = [];

                foreach ($cartItems as $item) {
                    $bookId = is_array($item) ? $item['id'] : $item->id;
                    $quantity = is_array($item) ? intval($item['quantity']) : intval($item->quantity);
                    $cartBookIds[] = $bookId;

                    // Acquire exclusive pessimistic lock on book row
                    $book = Book::where('id', $bookId)->lockForUpdate()->first();

                    if (!$book) {
                        throw new Exception("A volume in your bag is no longer listed in our catalog.");
                    }

                    // Calculate stock reserved by OTHER shoppers
                    $reservedByOthers = (int) StockReservation::active()
                        ->where('book_id', $bookId)
                        ->where(function ($query) use ($sessionId, $userId) {
                            $query->where('session_id', '!=', $sessionId);
                            if ($userId) {
                                $query->where(function ($q) use ($userId) {
                                    $q->whereNull('user_id')->orWhere('user_id', '!=', $userId);
                                });
                            }
                        })
                        ->sum('quantity');

                    $availableStock = max(0, $book->stock - $reservedByOthers);

                    if ($availableStock < $quantity) {
                        $remainingMsg = $availableStock === 0
                            ? "'{$book->title}' is currently reserved by other readers or out of stock."
                            : "'{$book->title}' only has {$availableStock} " . ($availableStock === 1 ? 'copy' : 'copies') . " available.";

                        throw new Exception($remainingMsg);
                    }

                    // Update or create reservation for this shopper
                    StockReservation::updateOrCreate(
                        [
                            'session_id' => $sessionId,
                            'book_id' => $bookId,
                        ],
                        [
                            'user_id' => $userId,
                            'quantity' => $quantity,
                            'expires_at' => $expiresAt,
                        ]
                    );
                }

                // Synchronize holds: release reservations for items no longer in the cart
                $orphanQuery = StockReservation::where(function ($q) use ($sessionId, $userId) {
                    $q->where('session_id', $sessionId);
                    if ($userId) {
                        $q->orWhere('user_id', $userId);
                    }
                });

                if (!empty($cartBookIds)) {
                    $orphanQuery->whereNotIn('book_id', $cartBookIds);
                }

                $orphanQuery->delete();

                return ['success' => true, 'error' => null];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Compute effective available stock for a book considering other users' active holds.
     */
    public static function getAvailableStock(Book $book, ?string $sessionId = null, ?int $userId = null): int
    {
        $query = StockReservation::active()->where('book_id', $book->id);

        if ($sessionId || $userId) {
            $query->where(function ($q) use ($sessionId, $userId) {
                if ($sessionId) {
                    $q->where('session_id', '!=', $sessionId);
                }
                if ($userId) {
                    $q->where(function ($inner) use ($userId) {
                        $inner->whereNull('user_id')->orWhere('user_id', '!=', $userId);
                    });
                }
            });
        }

        $reservedByOthers = (int) $query->sum('quantity');

        return max(0, $book->stock - $reservedByOthers);
    }

    /**
     * Release all reservations held by a user or session (e.g. upon payment or cart abandonment).
     */
    public static function releaseHolds(?int $userId = null, ?string $sessionId = null): void
    {
        if (!$userId && !$sessionId) {
            return;
        }

        StockReservation::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            }
            if ($sessionId) {
                $query->orWhere('session_id', $sessionId);
            }
        })->delete();
    }

    /**
     * Clean up any expired reservations older than current timestamp.
     */
    public static function pruneExpired(): int
    {
        return StockReservation::where('expires_at', '<=', now())->delete();
    }
}
