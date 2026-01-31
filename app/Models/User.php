<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Event;
use App\Models\Booking;
use App\Models\EventCategory;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\EventReview;
use App\Models\Organization;
use App\Models\UserConnection;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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
        'keycloak_id',
        'google_id',
        'github_id',
        'sso_provider',
        'profile_photo',
        'phone',
        'bio',
        'allow_connections',
        'show_profile_publicly',
        'show_email_to_connections',
        'show_phone_to_connections',
        'notification_preferences',
        'interested_category_ids',
        'newsletter_subscribed',
        'newsletter_subscribed_at',
        'allow_networking',
        'show_profile_public',
        'allow_data_analytics',
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
            'is_admin' => 'boolean',
            'notification_preferences' => 'array',
            'interested_category_ids' => 'array',
            'newsletter_subscribed_at' => 'datetime',
            'payout_settings' => 'array',
            'bank_account' => 'array',
            'organizer_billing_data' => 'array',
            'custom_platform_fee' => 'array',
            'allow_connections' => 'boolean',
            'show_profile_publicly' => 'boolean',
            'show_email_to_connections' => 'boolean',
            'show_phone_to_connections' => 'boolean',
            'allow_networking' => 'boolean',
            'show_profile_public' => 'boolean',
            'allow_data_analytics' => 'boolean',
            'invoice_settings' => 'array',
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
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
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
     * Get display name based on privacy settings
     * Returns full name for public profiles, anonymized name otherwise
     */
    public function displayName(?User $viewer = null): string
    {
        // Always show full name to the user themselves
        if ($viewer && $viewer->id === $this->id) {
            return $this->fullName();
        }

        // Show full name for public profiles
        if ($this->show_profile_public) {
            return $this->fullName();
        }

        // Show full name to connected users if networking is allowed
        if ($viewer && $this->allow_networking && $viewer->isFollowing($this)) {
            return $this->fullName();
        }

        // Otherwise, anonymize the name
        return $this->anonymizedName();
    }

    /**
     * Get anonymized name (e.g., "Max M." or "User #123")
     */
    public function anonymizedName(): string
    {
        if ($this->first_name) {
            $lastInitial = $this->last_name ? mb_substr($this->last_name, 0, 1) . '.' : '';
            return trim($this->first_name . ' ' . $lastInitial);
        }

        // Fallback: Use first part of name or User ID
        if ($this->name) {
            $parts = explode(' ', $this->name);
            if (count($parts) > 1) {
                return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.';
            }
            return $this->name;
        }

        return 'Benutzer #' . $this->id;
    }

    /**
     * Get the user's profile photo URL
     */
    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo) {
            return route('profile-photo.show', ['user' => $this->id]);
        }

        // Generate a gravatar URL as fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    /**
     * Get the events created by this user (through organizations)
     * Returns events from all organizations the user belongs to
     */
    public function events()
    {
        // Return a query builder, not a relation
        // This allows it to work with count(), get(), etc.
        return Event::query()
            ->whereHas('organization.users', function ($query) {
                $query->where('users.id', $this->id)
                      ->where('organization_user.is_active', true);
            });
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
     * Organizations this user belongs to
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot(['role', 'is_active', 'invited_at', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Active organizations only
     */
    public function activeOrganizations()
    {
        return $this->organizations()->wherePivot('is_active', true);
    }

    /**
     * Organizations where user is owner
     */
    public function ownedOrganizations()
    {
        return $this->organizations()->wherePivot('role', 'owner');
    }

    /**
     * Get current organization from session
     */
    public function currentOrganization(): ?Organization
    {
        $organizationId = session('current_organization_id');

        if (!$organizationId) {
            // Auto-select first active organization
            $organization = $this->activeOrganizations()->first();
            if ($organization) {
                session(['current_organization_id' => $organization->id]);
                return $organization;
            }
            return null;
        }

        return $this->activeOrganizations()->find($organizationId);
    }

    /**
     * Set current organization
     */
    public function setCurrentOrganization(Organization $organization): void
    {
        if (!$this->isMemberOf($organization)) {
            throw new \Exception('User is not a member of this organization');
        }

        session(['current_organization_id' => $organization->id]);
    }

    /**
     * Check if user is member of organization
     */
    public function isMemberOf(Organization $organization): bool
    {
        return $this->activeOrganizations()->where('organizations.id', $organization->id)->exists();
    }

    /**
     * Check if user is owner of organization
     */
    public function isOwnerOf(Organization $organization): bool
    {
        return $this->organizations()
            ->where('organizations.id', $organization->id)
            ->wherePivot('role', 'owner')
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Check if user can manage organization
     */
    public function canManageOrganization(Organization $organization): bool
    {
        if ($this->hasRole('admin')) {
            return true; // Platform admins can manage all
        }

        $pivot = $this->organizations()
            ->where('organizations.id', $organization->id)
            ->first()?->pivot;

        return $pivot && $pivot->is_active && in_array($pivot->role, ['owner', 'admin']);
    }

    /**
     * Get user's role in organization
     */
    public function getRoleInOrganization(Organization $organization): ?string
    {
        return $this->organizations()
            ->where('organizations.id', $organization->id)
            ->first()?->pivot?->role;
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
     * Check if organizer has complete billing data
     * Required fields: company_name, address, postal_code, city, country, email, phone, tax_id
     */
    public function hasCompleteBillingData(): bool
    {
        if (!$this->isOrganizer()) {
            return true; // Non-organizers don't need billing data
        }

        // For organizers with organization system, check current organization
        $organization = $this->currentOrganization();
        if ($organization) {
            return $organization->hasCompleteBillingData();
        }

        // Fallback to legacy user billing data (for backward compatibility)
        $billingData = $this->organizer_billing_data ?? [];

        // Check required fields
        $requiredFields = [
            'company_name',
            'company_address',
            'company_postal_code',
            'company_city',
            'company_country',
            'company_email',
            'company_phone',
            'tax_id'
        ];

        foreach ($requiredFields as $field) {
            if (empty($billingData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if organizer has complete bank account data
     * Required for receiving payments
     */
    public function hasCompleteBankAccount(): bool
    {
        if (!$this->isOrganizer()) {
            return true; // Non-organizers don't need bank account
        }

        // For organizers with organization system, check current organization
        $organization = $this->currentOrganization();
        if ($organization) {
            return $organization->hasCompleteBankAccount();
        }

        // Fallback to legacy user bank account (for backward compatibility)
        $bankAccount = $this->bank_account ?? [];

        // Check required fields: account_holder, iban
        return !empty($bankAccount['account_holder']) && !empty($bankAccount['iban']);
    }

    /**
     * Check if organizer can publish events
     * Requires complete billing data and bank account
     */
    public function canPublishEvents(): bool
    {
        if (!$this->isOrganizer()) {
            return false;
        }

        // For organizers with organization system, check current organization
        $organization = $this->currentOrganization();
        if ($organization) {
            return $organization->canPublishEvents();
        }

        // Fallback to legacy check (for backward compatibility)
        return $this->hasCompleteBillingData() && $this->hasCompleteBankAccount();
    }

    /**
     * Get missing organizer data as array
     * Returns list of missing requirements
     */
    public function getMissingOrganizerData(): array
    {
        if (!$this->isOrganizer()) {
            return [];
        }

        $missing = [];

        if (!$this->hasCompleteBillingData()) {
            $missing[] = 'billing_data';
        }

        if (!$this->hasCompleteBankAccount()) {
            $missing[] = 'bank_account';
        }

        return $missing;
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('manage users') || $this->hasRole('admin');
    }

    /**
     * Check if user is authenticated via SSO
     */
    public function isSsoUser(): bool
    {
        return !empty($this->sso_provider);
    }

    /**
     * Check if user can change password
     * SSO users cannot change password through the app
     */
    public function canChangePassword(): bool
    {
        return !$this->isSsoUser();
    }

    /**
     * Get SSO provider display name
     */
    public function ssoProviderName(): ?string
    {
        return $this->sso_provider ? ucfirst($this->sso_provider) : null;
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
            ->using(UserBadge::class)
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
    public function meetsRequirements(Badge $badge): bool
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

    /**
     * Get users that this user is following
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'follower_id', 'following_id')
            ->wherePivot('status', 'accepted')
            ->withPivot(['status', 'accepted_at'])
            ->withTimestamps();
    }

    /**
     * Get users that are following this user
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'following_id', 'follower_id')
            ->wherePivot('status', 'accepted')
            ->withPivot(['status', 'accepted_at'])
            ->withTimestamps();
    }

    /**
     * Get pending connection requests sent by this user
     */
    public function pendingFollowingRequests()
    {
        return $this->hasMany(UserConnection::class, 'follower_id')
            ->where('status', 'pending');
    }

    /**
     * Get pending connection requests received by this user
     */
    public function pendingFollowerRequests()
    {
        return $this->hasMany(UserConnection::class, 'following_id')
            ->where('status', 'pending');
    }

    /**
     * Get all connections (following and followers)
     */
    public function connections()
    {
        return $this->hasMany(UserConnection::class, 'follower_id')
            ->where(function ($query) {
                $query->where('follower_id', $this->id)
                    ->orWhere('following_id', $this->id);
            });
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * Check if this user is followed by another user
     */
    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    /**
     * Check if there is a pending connection request
     */
    public function hasPendingConnectionWith(User $user): bool
    {
        return UserConnection::where(function ($query) use ($user) {
            $query->where('follower_id', $this->id)
                ->where('following_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('follower_id', $user->id)
                ->where('following_id', $this->id);
        })->where('status', 'pending')->exists();
    }

    /**
     * Check if users are connected (mutual connection)
     */
    public function isConnectedWith(User $user): bool
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    /**
     * Get connection status with another user
     */
    public function getConnectionStatusWith(User $user): ?string
    {
        $connection = UserConnection::where(function ($query) use ($user) {
            $query->where('follower_id', $this->id)
                ->where('following_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('follower_id', $user->id)
                ->where('following_id', $this->id);
        })->first();

        return $connection?->status;
    }

    /**
     * Send connection request to another user
     */
    public function sendConnectionRequest(User $user): UserConnection
    {
        // Check if the other user already follows me (mutual connection)
        $existingConnection = UserConnection::where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', 'accepted')
            ->first();

        // If they already follow me, auto-accept the connection (mutual follow)
        $status = $existingConnection ? 'accepted' : 'pending';

        return UserConnection::create([
            'follower_id' => $this->id,
            'following_id' => $user->id,
            'status' => $status,
        ]);
    }

    /**
     * Accept connection request from another user
     */
    public function acceptConnectionRequest(User $user): bool
    {
        $connection = UserConnection::where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', 'pending')
            ->first();

        if ($connection) {
            $connection->accept();
            return true;
        }

        return false;
    }

    /**
     * Decline connection request from another user
     */
    public function declineConnectionRequest(User $user): bool
    {
        return UserConnection::where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', 'pending')
            ->delete() > 0;
    }

    /**
     * Remove connection with another user
     */
    public function removeConnection(User $user): bool
    {
        return UserConnection::where(function ($query) use ($user) {
            $query->where('follower_id', $this->id)
                ->where('following_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('follower_id', $user->id)
                ->where('following_id', $this->id);
        })->delete() > 0;
    }

    /**
     * Block a user
     */
    public function blockUser(User $user): UserConnection
    {
        // Remove existing connection if any
        $this->removeConnection($user);

        // Create blocked connection
        return UserConnection::create([
            'follower_id' => $this->id,
            'following_id' => $user->id,
            'status' => 'blocked',
        ]);
    }

    /**
     * Unblock a user
     */
    public function unblockUser(User $user): bool
    {
        return UserConnection::where('follower_id', $this->id)
            ->where('following_id', $user->id)
            ->where('status', 'blocked')
            ->delete() > 0;
    }

    /**
     * Check if user has blocked another user
     */
    public function hasBlocked(User $user): bool
    {
        return UserConnection::where('follower_id', $this->id)
            ->where('following_id', $user->id)
            ->where('status', 'blocked')
            ->exists();
    }

    /**
     * Get followers count
     */
    public function getFollowersCount(): int
    {
        return $this->followers()->count();
    }

    /**
     * Get following count
     */
    public function getFollowingCount(): int
    {
        return $this->following()->count();
    }

    /**
     * Get pending requests count
     */
    public function getPendingRequestsCount(): int
    {
        return $this->pendingFollowerRequests()->count();
    }

    /**
     * Get suggested connections (users with similar interests)
     *
     * Einschränkungen:
     * - schließt den aktuellen Nutzer aus
     * - schließt bereits bestehende Verbindungen (pending/accepted/blocked) aus
     * - zeigt nur Nutzer an, die Vernetzung erlauben (allow_networking = true)
     * - bei fehlenden Interessen: fallback auf aktivste Nutzer nach Buchungen (ebenfalls nur mit allow_networking)
     */
    public function getSuggestedConnections(int $limit = 10)
    {
        $categoryIds = $this->interested_category_ids ?? [];

        // Ursprüngliche Ermittlung der verbundenen IDs (alle Status) beibehalten
        $connectedUserIds = UserConnection::where(function ($query) {
            $query->where('follower_id', $this->id)
                ->orWhere('following_id', $this->id);
        })->get()->flatMap(function ($connection) {
            return [$connection->follower_id, $connection->following_id];
        })->unique()->filter(function ($id) {
            return $id !== $this->id;
        })->values()->toArray();

        if (empty($categoryIds)) {
            return User::where('id', '!=', $this->id)
                ->where('allow_networking', true)
                ->whereNotIn('id', $connectedUserIds)
                ->withCount('bookings')
                ->orderBy('bookings_count', 'desc')
                ->limit($limit)
                ->get();
        }

        $users = User::where('id', '!=', $this->id)
            ->where('allow_networking', true)
            ->whereNotIn('id', $connectedUserIds)
            ->get();

        $filteredUsers = $users->filter(function ($user) use ($categoryIds) {
            $userCategories = $user->interested_category_ids ?? [];
            if (empty($userCategories)) {
                return false;
            }
            return count(array_intersect($categoryIds, $userCategories)) > 0;
        })->take($limit);

        return $filteredUsers;
    }

    /**
     * Prüft ob dieses Profil vom gegebenen Betrachter eingesehen werden darf.
     * Bedingungen:
     * - Eigenes Profil immer sichtbar
     * - Öffentliches Profil (show_profile_public) sichtbar
     * - Wenn nicht öffentlich: sichtbar für Follower falls Vernetzung erlaubt
     * - Blockierte Beziehungen verhindern Sichtbarkeit
     */
    public function canBeViewedBy(?User $viewer): bool
    {
        // Eigenes Profil
        if ($viewer && $viewer->id === $this->id) {
            return true;
        }

        // Blockierung prüfen (falls Methoden existieren)
        if ($viewer && method_exists($viewer, 'hasBlocked') && method_exists($this, 'hasBlocked')) {
            if ($viewer->hasBlocked($this) || $this->hasBlocked($viewer)) {
                return false;
            }
        }

        // Öffentliches Profil
        if (isset($this->show_profile_public) && $this->show_profile_public === true) {
            return true;
        }

        // Legacy Fallback wenn Feld nicht gesetzt (behandle als öffentlich)
        if (!isset($this->show_profile_public)) {
            return true;
        }

        // Vernetzungs-Check für Follower
        if ($viewer && $viewer->isFollowing($this)) {
            $allowNetworking = isset($this->allow_networking) ? $this->allow_networking : true;
            if ($allowNetworking) {
                return true;
            }
        }

        return false;
    }
}
