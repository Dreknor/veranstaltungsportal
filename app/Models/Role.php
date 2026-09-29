<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property bool $is_system
 */
class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'description', 'color', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}

