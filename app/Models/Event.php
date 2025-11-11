<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Event extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'series_id',
        'series_position',
        'is_series_part',
        'event_category_id',
        'event_type',
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'duration',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_postal_code',
        'venue_country',
        'venue_latitude',
        'venue_longitude',
        'directions',
        'featured_image',
        'gallery_images',
        'video_url',
        'livestream_url',
        'online_url',
        'online_access_code',
        'price_from',
        'max_attendees',
        'is_published',
        'is_featured',
        'is_private',
        'registration_required',
        'access_code',
        'organizer_info',
        'organizer_email',
        'organizer_phone',
        'organizer_website',
        'meta_data',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'gallery_images' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'registration_required' => 'boolean',
            'meta_data' => 'array',
            'price_from' => 'decimal:2',
            'venue_latitude' => 'decimal:7',
            'venue_longitude' => 'decimal:7',
            'is_series_part' => 'boolean',
            'is_cancelled' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(EventSeries::class, 'series_id');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(EventReview::class);
    }

    public function discountCodes(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function featuredFees(): HasMany
    {
        return $this->hasMany(FeaturedEventFee::class);
    }

    public function activeFeaturedFee()
    {
        return $this->featuredFees()
            ->where('payment_status', 'paid')
            ->where('featured_start_date', '<=', now())
            ->where('featured_end_date', '>=', now())
            ->latest()
            ->first();
    }

    public function isPartOfSeries(): bool
    {
        return $this->is_series_part && $this->series_id !== null;
    }

    public function availableTickets(): int
    {
        if (!$this->max_attendees) {
            return PHP_INT_MAX;
        }

        // Calculate sold tickets through booking items
        // Include pending, confirmed and completed bookings
        $sold = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->with('items')
            ->get()
            ->flatMap(function ($booking) {
                return $booking->items;
            })
            ->sum('quantity');

        return max(0, $this->max_attendees - $sold);
    }

    /**
     * Check if event has available tickets
     */
    public function hasAvailableTickets(): bool
    {
        // First, check if event has reached max_attendees limit
        if ($this->max_attendees && $this->availableTickets() <= 0) {
            return false;
        }

        // If event has ticket types, check if any ticket type has availability and is on sale
        if ($this->ticketTypes()->exists()) {
            return $this->ticketTypes()
                ->where('is_available', true)
                ->get()
                ->contains(function ($ticketType) {
                    return $ticketType->availableQuantity() > 0 && $ticketType->isOnSale();
                });
        }

        // Otherwise check general availability based on max_attendees
        return $this->availableTickets() > 0;
    }

    public function averageRating(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * Get the minimum price for this event
     * Returns price_from if set, otherwise the cheapest available ticket type
     */
    public function getMinimumPrice(): ?float
    {
        // If price_from is set, use it
        if ($this->price_from !== null && $this->price_from > 0) {
            return (float) $this->price_from;
        }

        // Otherwise, get the minimum price from available ticket types
        $minTicketPrice = $this->ticketTypes()
            ->available()
            ->min('price');

        return $minTicketPrice ? (float) $minTicketPrice : null;
    }

    public function getLocationAttribute(): string
    {
        $parts = array_filter([
            $this->venue_name,
            $this->venue_address,
            $this->venue_city,
        ]);
        return implode(', ', $parts);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if this event can be booked individually
     */
    public function canBeBookedIndividually(): bool
    {
        return !$this->isPartOfSeries();
    }

    /**
     * Get the series this event belongs to
     */
    public function getParentSeries(): ?EventSeries
    {
        return $this->isPartOfSeries() ? $this->series : null;
    }

    /**
     * Check if event is an online event
     */
    public function isOnline(): bool
    {
        return $this->event_type === 'online';
    }

    /**
     * Check if event is a physical event
     */
    public function isPhysical(): bool
    {
        return $this->event_type === 'physical';
    }

    /**
     * Check if event is a hybrid event (both online and physical)
     */
    public function isHybrid(): bool
    {
        return $this->event_type === 'hybrid';
    }

    /**
     * Check if the event requires venue information
     */
    public function requiresVenue(): bool
    {
        return $this->event_type === 'physical' || $this->event_type === 'hybrid';
    }

    /**
     * Check if the event requires online information
     */
    public function requiresOnlineInfo(): bool
    {
        return $this->event_type === 'online' || $this->event_type === 'hybrid';
    }

    /**
     * Get confirmed attendees count
     */
    public function getAttendeesCount(): int
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();
    }

    /**
     * Check if event has any attendees
     */
    public function hasAttendees(): bool
    {
        return $this->getAttendeesCount() > 0;
    }

    /**
     * Get all confirmed attendees with their booking details
     */
    public function getAttendees()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->with('user')
            ->get();
    }

    /**
     * Calculate and update the event duration in minutes
     */
    public function calculateDuration(): void
    {
        if ($this->start_date && $this->end_date) {
            $this->duration = $this->start_date->diffInMinutes($this->end_date);
        }
    }

    /**
     * Get duration in hours
     */
    public function getDurationInHours(): float
    {
        return $this->duration ? round($this->duration / 60, 2) : 0;
    }

    /**
     * Get formatted duration string
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration) {
            return '0 Minuten';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} Stunden {$minutes} Minuten";
        } elseif ($hours > 0) {
            return "{$hours} " . ($hours === 1 ? 'Stunde' : 'Stunden');
        } else {
            return "{$minutes} " . ($minutes === 1 ? 'Minute' : 'Minuten');
        }
    }

    /**
     * Check if event can be booked
     */
    public function canBeBooked(): bool
    {
        return !$this->is_cancelled
            && $this->is_published
            && $this->start_date->isFuture()
            && $this->hasAvailableTickets();
    }

    /**
     * Increment the view count for this event
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Scope to order by popularity (views)
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }
}
