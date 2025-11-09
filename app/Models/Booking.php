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
        'payment_method',
        'payment_transaction_id',
        'discount_code_id',
        'additional_data',
        'confirmed_at',
        'cancelled_at',
        'certificate_generated_at',
        'certificate_path',
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
}
