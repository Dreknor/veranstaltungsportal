<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'ticket_type_id',
        'ticket_number',
        'attendee_name',
        'attendee_email',
        'attendee_organization',
        'ticket_sent_to',
        'ticket_sent_at',
        'price',
        'quantity',
        'custom_fields',
        'checked_in',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'custom_fields' => 'array',
            'checked_in' => 'boolean',
            'checked_in_at' => 'datetime',
            'ticket_sent_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->ticket_number) {
                $item->ticket_number = 'TK-' . strtoupper(uniqid());
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<BookingItemAttendance, $this>
     */
    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookingItemAttendance::class);
    }

    /**
     * Name der teilnehmenden Person (Fallback: Bucher).
     */
    public function participantName(): string
    {
        return $this->attendee_name ?: (string) $this->booking?->customer_name;
    }

    /**
     * Eigene E-Mail-Adresse der teilnehmenden Person, sofern sie vom Bucher abweicht.
     */
    public function separateAttendeeEmail(): ?string
    {
        $email = trim((string) $this->attendee_email);
        if ($email === '' || strcasecmp($email, (string) $this->booking?->customer_email) === 0) {
            return null;
        }

        return $email;
    }

    /**
     * Eingecheckt – bei Mehrfachterminen für den angegebenen Termin, sonst für die Veranstaltung.
     */
    public function isCheckedInFor(?EventDate $date = null): bool
    {
        if (!$date) {
            return (bool) $this->checked_in;
        }

        return $this->relationLoaded('attendances')
            ? $this->attendances->contains('event_date_id', $date->id)
            : $this->attendances()->where('event_date_id', $date->id)->exists();
    }

    public function checkInFor(?EventDate $date = null, ?User $by = null): void
    {
        if ($date) {
            $this->attendances()->firstOrCreate(
                ['event_date_id' => $date->id],
                ['checked_in_at' => now(), 'checked_in_by' => $by?->id]
            );
            $this->unsetRelation('attendances');
        }

        // Mindestens ein besuchter Termin zählt als Teilnahme (Zertifikat, Statistik)
        if (!$this->checked_in) {
            $this->update(['checked_in' => true, 'checked_in_at' => now()]);
        }
    }

    public function undoCheckInFor(?EventDate $date = null): void
    {
        if ($date) {
            $this->attendances()->where('event_date_id', $date->id)->delete();
            $this->unsetRelation('attendances');
            if ($this->attendances()->exists()) {
                return;
            }
        }

        $this->update(['checked_in' => false, 'checked_in_at' => null]);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
