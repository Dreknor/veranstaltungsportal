<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'notes',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket type
     */
    public function ticketType()
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
     * Mark as notified
     */
    public function markAsNotified()
    {
        $this->update([
            'status' => 'notified',
            'notified_at' => now(),
            'expires_at' => now()->addHours(48), // 48 Stunden Zeit zum Buchen
        ]);
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

