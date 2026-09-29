<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property bool $is_system
 */
class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = ['name', 'guard_name', 'group', 'description', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}


