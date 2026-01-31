<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quantity',
        'quantity_sold',
        'sale_start',
        'sale_end',
        'min_per_order',
        'max_per_order',
        'is_available',
        'included_services',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_start' => 'datetime',
            'sale_end' => 'datetime',
            'is_available' => 'boolean',
            'included_services' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function availableQuantity(): int
    {
        // First, calculate the available quantity for this specific ticket type
        $ticketTypeAvailable = ($this->quantity === null)
            ? PHP_INT_MAX
            : max(0, $this->quantity - $this->quantity_sold);

        // If the event has a max_attendees limit, respect that as well
        if ($this->event && $this->event->max_attendees) {
            $eventAvailable = $this->event->availableTickets();
            // Return the minimum of both limits
            return min($ticketTypeAvailable, $eventAvailable);
        }

        return $ticketTypeAvailable;
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('quantity')
              ->orWhereRaw('quantity > quantity_sold');
        })->where('is_available', true);
    }

    public function isOnSale(): bool
    {
        if (!$this->is_available) {
            return false;
        }

        $now = now();

        // Verkaufsstart prüfen
        if ($this->sale_start) {
            // Wenn Zeit auf 00:00:00 ist, wurde vermutlich nur ein Datum eingegeben
            // In diesem Fall: Verkauf beginnt am Anfang des Tages
            if ($this->sale_start->format('H:i:s') === '00:00:00') {
                // Vergleiche nur Datum - gleicher Tag oder später ist ok
                if ($now->toDateString() < $this->sale_start->toDateString()) {
                    return false;
                }
            } else {
                // Spezifische Zeit angegeben - verwende exakten Zeitvergleich
                if ($now->isBefore($this->sale_start)) {
                    return false;
                }
            }
        }

        // Verkaufsende prüfen
        if ($this->sale_end) {
            // Wenn Zeit auf 00:00:00 ist, gilt der ganze Tag bis 23:59:59
            if ($this->sale_end->format('H:i:s') === '00:00:00') {
                // Verkauf läuft bis Ende des Tages
                if ($now->toDateString() > $this->sale_end->toDateString()) {
                    return false;
                }
            } else {
                // Spezifische Zeit angegeben - verwende exakten Zeitvergleich
                if ($now->isAfter($this->sale_end)) {
                    return false;
                }
            }
        }

        return true;
    }
}
