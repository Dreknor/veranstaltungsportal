<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property bool $is_approved
 * @property bool $show_full_name
 * @property string|null $organizer_reply
 * @property \Illuminate\Support\Carbon|null $organizer_replied_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
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
        'show_full_name',
        'is_approved',
        'organizer_reply',
        'organizer_replied_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'show_full_name' => 'boolean',
            'organizer_replied_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Gesamtbewertung des Veranstalters ist zwischengespeichert – bei jeder Änderung verwerfen.
        $forget = function (EventReview $review) {
            $organizationId = $review->event()->value('organization_id');
            if ($organizationId) {
                Organization::forgetReviewSummary((int) $organizationId);
            }
        };

        static::saved($forget);
        static::deleted($forget);
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
     * Vollständiger Name der bewertenden Person (auch für Gastbuchungen ohne Benutzerkonto).
     * Für Veranstalter und Administration; öffentlich gilt publicName().
     */
    public function reviewerName(): string
    {
        return $this->user?->fullName() ?? $this->booking?->customer_name ?? 'Teilnehmer:in';
    }

    /**
     * Öffentlich angezeigter Name: „Daniela R.“, außer die Person hat den vollen Namen freigegeben.
     */
    public function publicName(): string
    {
        $name = trim($this->reviewerName());
        if ($this->show_full_name || $name === 'Teilnehmer:in') {
            return $name;
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        if (count($parts) < 2) {
            return $name;
        }

        $first = array_shift($parts);
        $last = (string) array_pop($parts);

        return $first.' '.Str::upper(Str::substr($last, 0, 1)).'.';
    }

    public function reviewerEmail(): ?string
    {
        return $this->user?->email ?? $this->booking?->customer_email;
    }

    public function isPending(): bool
    {
        return ! $this->is_approved && $this->rejected_at === null;
    }

    public function isRejected(): bool
    {
        return ! $this->is_approved && $this->rejected_at !== null;
    }

    public function hasOrganizerReply(): bool
    {
        return filled($this->organizer_reply);
    }

    /**
     * @param  Builder<EventReview>  $query
     * @return Builder<EventReview>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Wartet auf Prüfung: weder freigegeben noch abgelehnt.
     *
     * @param  Builder<EventReview>  $query
     * @return Builder<EventReview>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false)->whereNull('rejected_at');
    }

    /**
     * @param  Builder<EventReview>  $query
     * @return Builder<EventReview>
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('is_approved', false)->whereNotNull('rejected_at');
    }

    /**
     * Freigeben (hebt eine frühere Ablehnung auf).
     */
    public function approve(): void
    {
        $this->update(['is_approved' => true, 'rejected_at' => null]);
    }

    /**
     * Ablehnen – nur bei Verstoß gegen die Bewertungsrichtlinien.
     */
    public function reject(): void
    {
        $this->update(['is_approved' => false, 'rejected_at' => now()]);
    }

    /**
     * Zurück in die Prüfung (z. B. nachdem die Person ihre Bewertung geändert hat).
     */
    public function resetModeration(): void
    {
        $this->forceFill(['is_approved' => false, 'rejected_at' => null]);
    }
}
