<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_number',
        'invoice_number',
        'invoice_date',
        'event_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'email_verification_token',
        'email_verified_at',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_status',
        'tickets_personalized',
        'tickets_personalized_at',
        'payment_method',
        'payment_transaction_id',
        'discount_code_id',
        'additional_data',
        'confirmed_at',
        'cancelled_at',
        'certificate_generated_at',
        'certificate_path',
        'checked_in',
        'checked_in_at',
        'checked_in_by',
        'check_in_method',
        'check_in_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'additional_data' => 'array',
            'email_verified_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'certificate_generated_at' => 'datetime',
            'tickets_personalized' => 'boolean',
            'tickets_personalized_at' => 'datetime',
            'checked_in' => 'boolean',
            'checked_in_at' => 'datetime',
            'invoice_date' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = 'BK-' . strtoupper(uniqid());
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
    public function platformFee(): HasOne
    {
        return $this->hasOne(PlatformFee::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(EventReview::class);
    }

    /**
     * Check in this booking
     */
    public function checkIn(?User $checkedInBy = null, string $method = 'manual', ?string $notes = null): void
    {
        $this->update([
            'checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => $checkedInBy?->id,
            'check_in_method' => $method,
            'check_in_notes' => $notes,
        ]);
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn(): void
    {
        $this->update([
            'checked_in' => false,
            'checked_in_at' => null,
            'checked_in_by' => null,
            'check_in_method' => null,
            'check_in_notes' => null,
        ]);
    }

    /**
     * Check if booking can be checked in
     */
    public function canCheckIn(): bool
    {
        // Must be confirmed and paid
        if ($this->status !== 'confirmed' || $this->payment_status !== 'paid') {
            return false;
        }

        // Cannot check in if already checked in
        if ($this->checked_in) {
            return false;
        }

        // Event must not be in the future (allow check-in on event day)
        // Allow check-in up to 24 hours before the event for testing/early access
        if ($this->event && $this->event->start_date) {
            $eventStart = $this->event->start_date;
            $now = now();

            // Allow check-in if event is today or in the past, or within 24 hours
            if ($eventStart->isFuture() && !$eventStart->isToday() && $eventStart->diffInHours($now) > 24) {
                return false;
            }
        }

        return true;
    }


    public function getTotalAmountAttribute(): float
    {
        return $this->total;
    }

    public function getVerificationCodeAttribute(): string
    {
        // Generate a verification code from booking_number
        // Ensure we have at least 8 characters, pad if necessary
        $code = str_replace(['BK-', '-'], '', $this->booking_number);
        return strtoupper(substr($code, -8));
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if this booking needs ticket personalization
     */
    public function needsPersonalization(): bool
    {
        // Only needs personalization if:
        // 1. Payment is confirmed (paid)
        // 2. Has more than one ticket item
        // 3. Not yet personalized
        return $this->payment_status === 'paid'
            && $this->items()->count() > 1
            && !$this->tickets_personalized;
    }

    /**
     * Check if all tickets are personalized
     */
    public function allTicketsPersonalized(): bool
    {
        // If only one ticket, it's automatically personalized with buyer's name
        if ($this->items()->count() <= 1) {
            return true;
        }

        // Check if all items have attendee names using collection filter
        $unpersonalizedCount = $this->items->filter(function ($item) {
            return empty($item->attendee_name);
        })->count();

        return $unpersonalizedCount === 0;
    }

    /**
     * Check if tickets can be sent (paid and personalized if needed)
     */
    public function canSendTickets(): bool
    {
        return $this->payment_status === 'paid'
            && (!$this->needsPersonalization() || $this->tickets_personalized);
    }
}
