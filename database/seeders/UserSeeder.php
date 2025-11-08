<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'organizer',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Organizer User
        $organizer = User::create([
            'name' => 'Event Organizer',
            'email' => 'organizer@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'organizer',
            'organization_name' => 'EventPro GmbH',
            'organization_description' => 'Professionelle Event-Organisation seit 2020',
            'email_verified_at' => now(),
        ]);
        $organizer->assignRole('organizer');

        // Create Participant User
        $participant = User::create([
            'name' => 'Max Mustermann',
            'email' => 'participant@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'participant',
            'email_verified_at' => now(),
        ]);
        $participant->assignRole('user');

        // Create another Participant
        $participant2 = User::create([
            'name' => 'Anna Schmidt',
            'email' => 'anna@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'participant',
            'email_verified_at' => now(),
        ]);
        $participant2->assignRole('user');
    }
}

