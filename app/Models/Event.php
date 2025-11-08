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
        'event_category_id',
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
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
        'price_from',
        'max_attendees',
        'is_published',
        'is_featured',
        'is_private',
        'access_code',
        'organizer_info',
        'organizer_email',
        'organizer_phone',
        'organizer_website',
        'meta_data',
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
            'meta_data' => 'array',
            'price_from' => 'decimal:2',
            'venue_latitude' => 'decimal:7',
            'venue_longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(EventReview::class);
    }

    public function discountCodes(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function availableTickets(): int
    {
        if (!$this->max_attendees) {
            return PHP_INT_MAX;
        }

        // Calculate sold tickets through booking items
        $sold = $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
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
}
