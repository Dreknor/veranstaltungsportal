<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRegistrationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'email',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function createForUser(User $user, int $daysValid = 7): self
    {
        return self::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
            'email' => $user->email,
            'expires_at' => now()->addDays($daysValid),
        ]);
    }
}
