<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'event_id',
        'booking_id',
        'user_id',
        'type', // platform_fee, participant
        'recipient_name',
        'recipient_email',
        'recipient_address',
        'amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'currency',
        'invoice_date',
        'due_date',
        'paid_at',
        'status', // sent, paid, overdue, cancelled
        'billing_data',
        'pdf_path',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'billing_data' => 'array',
    ];

    /**
     * Get the event that owns the invoice
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the booking that owns the invoice
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user (organizer) that owns the invoice
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Scope for platform fee invoices
     */
    public function scopePlatformFee($query)
    {
        return $query->where('type', 'platform_fee');
    }

    /**
     * Scope for participant invoices
     */
    public function scopeParticipant($query)
    {
        return $query->where('type', 'participant');
    }

    /**
     * Scope for overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                     ->where('due_date', '<', now());
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->total_amount, 2, ',', '.') . ' ' . $this->currency;
    }
}

