<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property bool $is_approved
 */
class EventReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'user_id',
        'booking_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Anzeigename der bewertenden Person (auch für Gastbuchungen ohne Benutzerkonto).
     */
    public function reviewerName(): string
    {
        return $this->user?->name ?? $this->booking?->customer_name ?? 'Teilnehmer:in';
    }

    public function reviewerEmail(): ?string
    {
        return $this->user?->email ?? $this->booking?->customer_email;
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
