<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'start_date',
        'end_date',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_postal_code',
        'venue_country',
        'venue_latitude',
        'venue_longitude',
        'notes',
        'is_cancelled',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'venue_latitude' => 'decimal:7',
            'venue_longitude' => 'decimal:7',
            'is_cancelled' => 'boolean',
        ];
    }

    /**
     * Get the event this date belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get venue name (fallback to event venue)
     */
    public function getVenueNameAttribute($value): ?string
    {
        return $value ?? $this->event?->venue_name;
    }

    /**
     * Get venue address (fallback to event venue)
     */
    public function getVenueAddressAttribute($value): ?string
    {
        return $value ?? $this->event?->venue_address;
    }

    /**
     * Get venue city (fallback to event venue)
     */
    public function getVenueCityAttribute($value): ?string
    {
        return $value ?? $this->event?->venue_city;
    }

    /**
     * Get venue postal code (fallback to event venue)
     */
    public function getVenuePostalCodeAttribute($value): ?string
    {
        return $value ?? $this->event?->venue_postal_code;
    }

    /**
     * Get venue country (fallback to event venue)
     */
    public function getVenueCountryAttribute($value): ?string
    {
        return $value ?? $this->event?->venue_country;
    }

    /**
     * Get full location string
     */
    public function getLocationAttribute(): string
    {
        $parts = array_filter([
            $this->venue_name,
            $this->venue_address,
            $this->venue_city,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Check if this date is in the future
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if this date is in the past
     */
    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Scope for upcoming dates
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * Scope for past dates
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope for active (not cancelled) dates
     */
    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }
}

