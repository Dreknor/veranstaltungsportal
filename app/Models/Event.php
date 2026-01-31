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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'organization_id',
        'series_id',
        'series_position',
        'event_category_id',
        'event_type',
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'duration',
        'has_multiple_dates',
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
        'online_url',
        'online_access_code',
        'price_from',
        'max_attendees',
        'is_published',
        'is_featured',
        'is_private',
        'registration_required',
        'access_code',
        'meta_data',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason',
        'ticket_notes',
        'show_qr_code_on_ticket',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'has_multiple_dates' => 'boolean',
            'gallery_images' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'registration_required' => 'boolean',
            'meta_data' => 'array',
            'price_from' => 'decimal:2',
            'venue_latitude' => 'decimal:7',
            'venue_longitude' => 'decimal:7',
            'is_cancelled' => 'boolean',
            'cancelled_at' => 'datetime',
            'show_qr_code_on_ticket' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Pseudo-relation for backwards compatibility with tests
     * Returns the first owner of the organization
     */
    public function user()
    {
        if ($this->organization) {
            return $this->organization->users()->wherePivot('role', 'owner')->first();
        }
        return null;
    }

    /**
     * Alias for organization (for backwards compatibility)
     */
    public function organizer(): BelongsTo
    {
        return $this->organization();
    }

    /**
     * Get the organization or null
     */
    public function getOrganizerEntity()
    {
        return $this->organization;
    }

    /**
     * Get organizer name (prefer organization)
     */
    public function getOrganizerName(): string
    {
        if ($this->organization) {
            return $this->organization->name;
        }
        return 'Unbekannt';
    }

    public function getOrganizerEmail(): ?string
    {
        return $this->organization?->email;
    }

    public function getOrganizerPhone(): ?string
    {
        return $this->organization?->phone;
    }

    public function getOrganizerWebsite(): ?string
    {
        return $this->organization?->website;
    }

    public function getOrganizerInfo(): ?string
    {
        return $this->organization?->description;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    /**
     * Get all dates for this event (for events with multiple dates)
     */
    public function dates(): HasMany
    {
        return $this->hasMany(EventDate::class)->orderBy('start_date');
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

    /**
     * Check if this event has multiple dates
     */
    public function hasMultipleDates(): bool
    {
        return $this->has_multiple_dates && $this->dates()->count() > 0;
    }

    /**
     * Get all event dates (includes main date if single event)
     */
    public function getAllDates(): \Illuminate\Support\Collection
    {
        if ($this->hasMultipleDates()) {
            return $this->dates;
        }

        // For single events, return main start/end date as collection
        return collect([
            (object)[
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'venue_name' => $this->venue_name,
                'venue_address' => $this->venue_address,
                'venue_city' => $this->venue_city,
                'venue_postal_code' => $this->venue_postal_code,
                'venue_country' => $this->venue_country,
                'is_cancelled' => $this->is_cancelled,
            ]
        ]);
    }

    /**
     * Get next upcoming date for this event
     */
    public function getNextDate()
    {
        if ($this->hasMultipleDates()) {
            return $this->dates()
                ->where('start_date', '>', now())
                ->where('is_cancelled', false)
                ->orderBy('start_date')
                ->first();
        }

        return $this->start_date > now() ? $this : null;
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
     * Check if event has available tickets (either ticket types or base price)
     */
    public function hasAvailableTickets(): bool
    {
        // First, check if event has reached max_attendees limit
        if ($this->max_attendees && $this->availableTickets() <= 0) {
            return false;
        }

        // If event has ticket types, check if any are available
        $hasAvailableTicketType = false;
        if ($this->ticketTypes()->exists()) {
            $hasAvailableTicketType = $this->ticketTypes()
                ->where('is_available', true)
                ->get()
                ->contains(function ($ticketType) {
                    // Check if ticket is on sale (considering sale_start and sale_end)
                    if (!$ticketType->isOnSale()) {
                        return false;
                    }

                    // If quantity is null (unlimited), it's available
                    if ($ticketType->quantity === null) {
                        return true;
                    }

                    // Otherwise check if there's quantity available
                    return $ticketType->availableQuantity() > 0;
                });
        }

        // Event is bookable if:
        // 1. At least one ticket type is available OR
        // 2. Event has a base price (price_from) set
        // This allows for both ticket types AND price_from to coexist
        if ($hasAvailableTicketType) {
            return true;
        }

        // If no ticket types are available, check if price_from is set
        if ($this->price_from !== null && $this->price_from >= 0) {
            return true;
        }

        // Otherwise check general availability based on max_attendees
        return $this->availableTickets() > 0;
    }

    /**
     * Check if event is sold out
     */
    public function isSoldOut(): bool
    {
        return !$this->hasAvailableTickets();
    }

    public function averageRating(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * Get the minimum price for this event
     * Returns the lowest price between price_from and available ticket types
     */
    public function getMinimumPrice(): ?float
    {
        $prices = [];

        // Add price_from if set
        if ($this->price_from !== null && $this->price_from >= 0) {
            $prices[] = (float) $this->price_from;
        }

        // Add minimum price from available ticket types
        $minTicketPrice = $this->ticketTypes()
            ->where('is_available', true)
            ->get()
            ->filter(fn($t) => $t->isOnSale() && $t->availableQuantity() > 0)
            ->min('price');

        if ($minTicketPrice !== null) {
            $prices[] = (float) $minTicketPrice;
        }

        // Return the minimum of all prices, or null if no prices exist
        return !empty($prices) ? min($prices) : null;
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

    /**
     * Get the user associated with this event (for legacy compatibility)
     * Returns first owner/admin of organization
     */
    public function getUser(): ?\App\Models\User
    {
        // Prefer first owner/admin of organization for compatibility
        return $this->organization?->owners()->first()
            ?? $this->organization?->admins()->first()
            ?? $this->organization?->users()->first();
    }

    /**
     * Accessor for user attribute (for backward compatibility)
     */
    public function getUserAttribute(): ?\App\Models\User
    {
        // Check if 'user' is already in attributes (to avoid recursion)
        if (array_key_exists('user', $this->attributes)) {
            return $this->attributes['user'];
        }

        return $this->getUser();
    }

    // Allow setting user_id in factories/tests to map to organization_id
    public function setUserIdAttribute($value): void
    {
        $user = \App\Models\User::find($value);
        if ($user) {
            $org = $user->activeOrganizations()->first();
            if (!$org) {
                $org = \App\Models\Organization::factory()->create();
                $org->users()->attach($user->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);
            }
            $this->attributes['organization_id'] = $org->id;
        }
    }
}
