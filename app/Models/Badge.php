<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array<array-key, mixed>|null $requirements
 * @property bool $is_active
 * @property int|null $points
 */
class Badge extends Model
{
    use HasFactory;

    /**
     * Anforderungen, die die Vergabelogik (User::meetsRequirements, BadgeService) auswertet.
     */
    public const REQUIREMENT_TYPES = [
        'events_attended' => 'Events besucht (eingecheckt)',
        'total_hours_attended' => 'Gesamt-Stunden teilgenommen',
        'bookings_count' => 'Bezahlte Buchungen',
        'reviews_written' => 'Bewertungen geschrieben',
        'connections_made' => 'Verbindungen hergestellt',
        'events_organized' => 'Events organisiert',
        'categories_explored' => 'Verschiedene Kategorien besucht',
        'early_bird_bookings' => 'Frühbucher-Buchungen (7+ Tage vorher)',
    ];

    /**
     * Ältere Bezeichnungen aus früheren Formularversionen.
     */
    public const REQUIREMENT_ALIASES = [
        'bookings_made' => 'bookings_count',
        'event_categories' => 'categories_explored',
    ];

    public static function normalizeRequirement(string $key): string
    {
        return self::REQUIREMENT_ALIASES[$key] ?? $key;
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image_path',
        'color',
        'type',
        'requirements',
        'points',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    /**
     * Users who have earned this badge
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->using(UserBadge::class)
            ->withPivot(['earned_at', 'is_highlighted', 'progress'])
            ->withTimestamps();
    }

    /**
     * Check if a user has earned this badge
     */
    public function isEarnedBy(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Award this badge to a user
     */
    public function awardTo(User $user): void
    {
        if (!$this->isEarnedBy($user)) {
            $this->users()->attach($user->id, [
                'earned_at' => now(),
            ]);
        }
    }

    /**
     * Get badge icon URL
     */
    public function getIconUrlAttribute(): string
    {
        if ($this->icon && file_exists(public_path($this->icon))) {
            return asset($this->icon);
        }

        // Default icon based on type
        return match($this->type) {
            'attendance' => asset('images/badges/attendance-default.svg'),
            'special' => asset('images/badges/special-default.svg'),
            default => asset('images/badges/achievement-default.svg'),
        };
    }

    /**
     * Scope for active badges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for badges by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

