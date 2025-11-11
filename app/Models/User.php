<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'organization_name',
        'organization_website',
        'organization_description',
        'profile_photo',
        'phone',
        'bio',
        'notification_preferences',
        'interested_category_ids',
        'newsletter_subscribed',
        'newsletter_subscribed_at',
        'payout_settings',
        'bank_account',
        'organizer_billing_data',
        'custom_platform_fee',
        'billing_company',
        'billing_address',
        'billing_address_line2',
        'billing_postal_code',
        'billing_city',
        'billing_state',
        'billing_country',
        'tax_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'interested_category_ids' => 'array',
            'newsletter_subscribed_at' => 'datetime',
            'payout_settings' => 'array',
            'bank_account' => 'array',
            'organizer_billing_data' => 'array',
            'custom_platform_fee' => 'array',
        ];
    }

    /**
     * Get notification preferences with default values
     */
    protected function notificationPreferences(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_array($value) ? $value : [],
        );
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }

        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name
     */
    public function fullName(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return $this->name;
    }

    /**
     * Get the user's profile photo URL
     */
    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        // Generate a gravatar URL as fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    /**
     * Get the events created by this user
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the bookings made by this user
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the user's favorite events
     */
    public function favoriteEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user_favorites')
            ->withTimestamps();
    }

    /**
     * Check if user has favorited an event
     */
    public function hasFavorited(Event $event): bool
    {
        return $this->favoriteEvents()->where('event_id', $event->id)->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is organizer
     */
    public function isOrganizer(): bool
    {
        return $this->hasRole('organizer');
    }

    /**
     * Check if user is participant (has user role but not organizer/admin)
     */
    public function isParticipant(): bool
    {
        return $this->hasRole('user') && !$this->hasRole(['organizer', 'admin']);
    }

    /**
     * Get user type label based on roles
     */
    public function userTypeLabel(): string
    {
        if ($this->hasRole('admin')) {
            return 'Administrator';
        }

        if ($this->hasRole('organizer')) {
            return 'Organisator';
        }

        return 'Teilnehmer';
    }

    /**
     * Check if user can manage events
     */
    public function canManageEvents(): bool
    {
        return $this->hasPermissionTo('manage events') || $this->hasRole(['admin', 'organizer']);
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('manage users') || $this->hasRole('admin');
    }

    /**
     * Get user's interested categories
     */
    public function interestedCategories()
    {
        $categoryIds = $this->interested_category_ids ?? [];
        return EventCategory::whereIn('id', $categoryIds)->get();
    }

    /**
     * Check if user is interested in a category
     */
    public function isInterestedInCategory(int $categoryId): bool
    {
        $categoryIds = $this->interested_category_ids ?? [];
        return in_array($categoryId, $categoryIds);
    }

    /**
     * Add category to interests
     */
    public function addInterest(int $categoryId): void
    {
        $categoryIds = $this->interested_category_ids ?? [];
        if (!in_array($categoryId, $categoryIds)) {
            $categoryIds[] = $categoryId;
            $this->interested_category_ids = $categoryIds;
            $this->save();
        }
    }

    /**
     * Remove category from interests
     */
    public function removeInterest(int $categoryId): void
    {
        $categoryIds = $this->interested_category_ids ?? [];
        $categoryIds = array_filter($categoryIds, fn($id) => $id != $categoryId);
        $this->interested_category_ids = array_values($categoryIds);
        $this->save();
    }

    /**
     * Get recommended events based on user interests
     */
    public function getRecommendedEvents(int $limit = 6)
    {
        $categoryIds = $this->interested_category_ids ?? [];

        if (empty($categoryIds)) {
            // If no interests, return popular events
            return Event::published()
                ->where('start_date', '>', now())
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get();
        }

        return Event::published()
            ->whereIn('event_category_id', $categoryIds)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribeToNewsletter(): void
    {
        $this->newsletter_subscribed = true;
        $this->newsletter_subscribed_at = now();
        $this->save();
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribeFromNewsletter(): void
    {
        $this->newsletter_subscribed = false;
        $this->save();
    }

    /**
     * Get user's earned badges
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot(['earned_at', 'is_highlighted', 'progress'])
            ->withTimestamps()
            ->orderByPivot('earned_at', 'desc');
    }

    /**
     * Get highlighted badges
     */
    public function highlightedBadges()
    {
        return $this->badges()->wherePivot('is_highlighted', true);
    }

    /**
     * Check if user has earned a specific badge
     */
    public function hasBadge(int|string $badgeIdOrSlug): bool
    {
        if (is_numeric($badgeIdOrSlug)) {
            return $this->badges()->where('badge_id', $badgeIdOrSlug)->exists();
        }

        return $this->badges()->where('slug', $badgeIdOrSlug)->exists();
    }

    /**
     * Award badge to user
     */
    public function awardBadge(Badge $badge): void
    {
        if (!$this->hasBadge($badge->id)) {
            $this->badges()->attach($badge->id, [
                'earned_at' => now(),
            ]);
        }
    }

    /**
     * Get total badge points
     */
    public function getTotalBadgePoints(): int
    {
        return $this->badges()->sum('points');
    }

    /**
     * Get badge count
     */
    public function getBadgeCount(): int
    {
        return $this->badges()->count();
    }

    /**
     * Toggle badge highlight status
     */
    public function toggleBadgeHighlight(int $badgeId): void
    {
        $badge = $this->badges()->where('badge_id', $badgeId)->first();

        if ($badge) {
            $this->badges()->updateExistingPivot($badgeId, [
                'is_highlighted' => !$badge->pivot->is_highlighted,
            ]);
        }
    }

    /**
     * Check and award badges based on user activity
     */
    public function checkAndAwardBadges(): void
    {
        $allBadges = Badge::active()->get();

        foreach ($allBadges as $badge) {
            if (!$this->hasBadge($badge->id) && $this->meetsRequirements($badge)) {
                $this->awardBadge($badge);
            }
        }
    }

    /**
     * Check if user meets badge requirements
     */
    protected function meetsRequirements(Badge $badge): bool
    {
        $requirements = $badge->requirements ?? [];

        foreach ($requirements as $key => $value) {
            switch ($key) {
                case 'bookings_count':
                    if ($this->bookings()->where('payment_status', 'paid')->count() < $value) {
                        return false;
                    }
                    break;

                case 'events_attended':
                    if ($this->bookings()->where('checked_in', true)->count() < $value) {
                        return false;
                    }
                    break;

                case 'events_organized':
                    if ($this->events()->count() < $value) {
                        return false;
                    }
                    break;

                case 'reviews_written':
                    if (EventReview::where('user_id', $this->id)->count() < $value) {
                        return false;
                    }
                    break;

                case 'total_hours_attended':
                    $totalHours = $this->bookings()
                        ->where('checked_in', true)
                        ->join('events', 'bookings.event_id', '=', 'events.id')
                        ->sum('events.duration');
                    if ($totalHours < $value) {
                        return false;
                    }
                    break;

                case 'categories_explored':
                    $categoriesCount = $this->bookings()
                        ->join('events', 'bookings.event_id', '=', 'events.id')
                        ->distinct('events.event_category_id')
                        ->count('events.event_category_id');
                    if ($categoriesCount < $value) {
                        return false;
                    }
                    break;

                case 'early_bird_bookings':
                    $earlyBirdCount = $this->bookings()
                        ->join('events', 'bookings.event_id', '=', 'events.id')
                        ->whereRaw('bookings.created_at < DATE_SUB(events.start_date, INTERVAL 7 DAY)')
                        ->count();
                    if ($earlyBirdCount < $value) {
                        return false;
                    }
                    break;

                case 'streak_days':
                    // Consecutive days with event attendance - simplified check
                    // In production, you'd implement a more sophisticated streak tracking
                    return true; // Placeholder
                    break;

                default:
                    return true;
            }
        }

        return true;
    }
}
