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
        if ($this->quantity === null) {
            return PHP_INT_MAX;
        }

        return max(0, $this->quantity - $this->quantity_sold);
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

        if ($this->sale_start && $now->lt($this->sale_start)) {
            return false;
        }

        if ($this->sale_end && $now->gt($this->sale_end)) {
            return false;
        }

        return true;
    }
}
