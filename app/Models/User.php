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
        'keycloak_id',
        'google_id',
        'github_id',
        'sso_provider',
        'organization_name',
        'organization_website',
        'organization_description',
        'profile_photo',
        'phone',
        'bio',
        'notification_preferences',
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
}
