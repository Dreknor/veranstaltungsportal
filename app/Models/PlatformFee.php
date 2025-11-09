<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'booking_id',
        'fee_percentage',
        'booking_amount',
        'fee_amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'fee_percentage' => 'decimal:2',
        'booking_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the event that owns the platform fee
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the booking that owns the platform fee
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if fee is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }

    /**
     * Mark fee as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}

