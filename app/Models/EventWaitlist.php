<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Illuminate\Support\Carbon|null $notified_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 */
class EventWaitlist extends Model
{
    use HasFactory;

    protected $table = 'event_waitlist';

    protected $fillable = [
        'event_id',
        'user_id',
        'email',
        'name',
        'phone',
        'quantity',
        'ticket_type_id',
        'status',
        'notified_at',
        'expires_at',
        'claim_token',
        'notes',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the event
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket type
     *
     * @return BelongsTo<TicketType, $this>
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Scope for waiting entries
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope for notified entries
     */
    public function scopeNotified($query)
    {
        return $query->where('status', 'notified');
    }

    /**
     * Scope for non-expired entries
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Aktive Reservierungen (benachrichtigt, Frist läuft noch)
     */
    public function scopeActiveReservation($query)
    {
        return $query->where('status', 'notified')->where('expires_at', '>', now());
    }

    /**
     * Mark as notified – reserviert die Plätze und erzeugt einen persönlichen Buchungslink
     */
    public function markAsNotified()
    {
        $this->update([
            'status' => 'notified',
            'notified_at' => now(),
            'expires_at' => now()->addHours(\App\Services\WaitlistService::RESERVATION_HOURS),
            'claim_token' => \Illuminate\Support\Str::random(40),
        ]);
    }

    /**
     * Persönlicher Buchungslink für die Reservierung
     */
    public function claimUrl(): string
    {
        return route('bookings.create', ['event' => $this->event, 'waitlist' => $this->claim_token]);
    }

    /**
     * Mark as converted
     */
    public function markAsConverted()
    {
        $this->update([
            'status' => 'converted',
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Check if entry is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

