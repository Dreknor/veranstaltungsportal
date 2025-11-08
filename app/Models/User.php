<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'user_type',
        'organization_name',
        'organization_description',
        'profile_photo',
        'phone',
        'bio',
        'is_organizer',
        'notification_preferences',
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
            'is_organizer' => 'boolean',
            'notification_preferences' => 'array',
        ];
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
     * Check if user is organizer (by user_type or role)
     */
    public function isOrganizer(): bool
    {
        return $this->user_type === 'organizer' || $this->hasRole('organizer') || $this->is_organizer;
    }

    /**
     * Check if user is participant
     */
    public function isParticipant(): bool
    {
        return $this->user_type === 'participant';
    }

    /**
     * Get user type label
     */
    public function userTypeLabel(): string
    {
        return match($this->user_type) {
            'organizer' => 'Organisator',
            'participant' => 'Teilnehmer',
            default => 'Benutzer',
        };
    }

    /**
     * Check if user can manage events
     */
    public function canManageEvents(): bool
    {
        return $this->hasPermissionTo('manage events') || $this->hasRole(['admin', 'organizer']) || $this->isOrganizer();
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('manage users') || $this->hasRole('admin');
    }
}
