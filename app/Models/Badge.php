<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
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
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
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

