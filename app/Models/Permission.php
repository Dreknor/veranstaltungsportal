<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name', 'group', 'description', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}

