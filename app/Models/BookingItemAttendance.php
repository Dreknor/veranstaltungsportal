<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Anwesenheit eines Tickets an einem einzelnen Termin (Veranstaltungen mit mehreren Terminen).
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 */
class BookingItemAttendance extends Model
{
    protected $fillable = [
        'booking_item_id',
        'event_date_id',
        'checked_in_at',
        'checked_in_by',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<BookingItem, $this>
     */
    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class);
    }

    /**
     * @return BelongsTo<EventDate, $this>
     */
    public function eventDate(): BelongsTo
    {
        return $this->belongsTo(EventDate::class);
    }
}
