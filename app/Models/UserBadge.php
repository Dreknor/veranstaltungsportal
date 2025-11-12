<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBadge extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_badges';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
            'is_highlighted' => 'boolean',
            'progress' => 'integer',
        ];
    }
}

