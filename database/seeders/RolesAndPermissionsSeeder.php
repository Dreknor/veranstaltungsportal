<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Event Management
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            'feature events',
            'manage own events',

            // Booking Permissions
            'view bookings',
            'create bookings',
            'cancel bookings',
            'manage own bookings',
            'manage all bookings',
            'export bookings',

            // User Permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',

            // Review Permissions
            'view reviews',
            'create reviews',
            'edit reviews',
            'delete reviews',
            'moderate reviews',

            // Ticket Type Permissions
            'manage ticket types',

            // Discount Code Permissions
            'manage discount codes',

            // Category Permissions
            'manage categories',

            // Statistics & Reports
            'view statistics',
            'view reports',
            'export reports',

            // System Permissions
            'access admin panel',
            'manage settings',
            'view audit log',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions

        // 1. Admin Role - Full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Organizer Role - Event and booking management
        $organizerRole = Role::create(['name' => 'organizer']);
        $organizerRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
            'manage own events',
            'view bookings',
            'manage all bookings',
            'export bookings',
            'manage ticket types',
            'manage discount codes',
            'view reviews',
            'moderate reviews',
            'view statistics',
            'view reports',
        ]);

        // 3. User Role - Basic user permissions
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view events',
            'create bookings',
            'cancel bookings',
            'manage own bookings',
            'create reviews',
            'edit reviews',
        ]);

        // 4. Moderator Role - Content moderation
        $moderatorRole = Role::create(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view events',
            'view bookings',
            'view reviews',
            'moderate reviews',
            'view users',
        ]);

        // 5. Viewer Role - Read-only access
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view events',
            'view bookings',
            'view reviews',
            'view statistics',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles: admin, organizer, user, moderator, viewer');
        $this->command->info('Total Permissions: ' . count($permissions));
    }
}
