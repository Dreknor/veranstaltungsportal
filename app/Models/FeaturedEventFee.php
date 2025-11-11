<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedEventFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'duration_type',
        'duration_days',
        'featured_start_date',
        'featured_end_date',
        'fee_amount',
        'payment_status',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'featured_start_date' => 'date',
        'featured_end_date' => 'date',
        'fee_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the event that owns the featured fee
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user (organizer) that owns the featured fee
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if fee is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' && $this->paid_at !== null;
    }

    /**
     * Check if featured period is active
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();
        return $this->isPaid()
            && $this->featured_start_date <= $now
            && $this->featured_end_date >= $now;
    }

    /**
     * Check if featured period has expired
     */
    public function isExpired(): bool
    {
        return $this->featured_end_date < now()->toDateString();
    }

    /**
     * Mark fee as paid
     */
    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    /**
     * Mark fee as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }

    /**
     * Refund the fee
     */
    public function refund(): void
    {
        $this->update([
            'payment_status' => 'refunded',
        ]);
    }

    /**
     * Calculate fee amount based on duration type
     */
    public static function calculateFee(string $durationType, int $customDays = null): float
    {
        $rates = config('monetization.featured_event_rates');

        return match($durationType) {
            'daily' => $rates['daily'],
            'weekly' => $rates['weekly'],
            'monthly' => $rates['monthly'],
            'custom' => $customDays ? ($rates['daily'] * $customDays) : 0,
            default => 0,
        };
    }
}

