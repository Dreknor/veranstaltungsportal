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
        try {
            Permission::create(['name' => 'receive-critical-log-notifications']);

            // Optional: Gib Admin-Rolle automatisch diese Permission
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $adminRole->givePermissionTo('receive-critical-log-notifications');
            }
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Fehler beim Erstellen der Permission: ' . $e->getMessage());
        }
        // Erstelle die Permission

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'receive-critical-log-notifications')->delete();
    }
};

