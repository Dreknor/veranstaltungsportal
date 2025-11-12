<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Erstelle die Permission
        Permission::create(['name' => 'receive-critical-log-notifications']);

        // Optional: Gib Admin-Rolle automatisch diese Permission
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo('receive-critical-log-notifications');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'receive-critical-log-notifications')->delete();
    }
};

